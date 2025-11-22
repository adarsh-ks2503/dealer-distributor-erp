<?php

namespace App\Imports;

use App\Models\Stock;
use App\Models\ItemName;
use App\Models\Warehouse;
use App\Services\StockTransactionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport implements ToCollection, WithHeadingRow
{
    protected $skippedRows = [];
    public $changed = false;

    public function collection(Collection $rows)
    {
        $rowNumber = 2; // Start from data row (heading = row 1)
        $validRows = [];

        foreach ($rows as $row) {
            $quantity = trim($row['quantity'] ?? '');
            
            // ✅ 1. Skip rows with no quantity (do nothing, no error)
            if ($quantity === '' || !ctype_digit($quantity) || intval($quantity) <= 0) {
                $rowNumber++;
                continue;
            }
            // dd($row);

            // ✅ 2. Now validate only those rows with quantity
            $warehouseId = trim($row['warehouse_id'] ?? '');
            $itemName    = trim($row['item_name'] ?? '');
            $itemSize    = trim($row['item_size'] ?? '');
            $length      = trim($row['length'] ?? '');

            $errors = [];

            if ($warehouseId === '') $errors[] = 'Warehouse ID missing';
            if ($itemName === '') $errors[]    = 'Item Name missing';
            if ($itemSize === '') $errors[]    = 'Item Size missing';
            if ($length === '') $errors[]      = 'Length missing';

            // Check relations only if fields are filled
            if (empty($errors)) {
                $warehouse = Warehouse::find($warehouseId);
                $item = ItemName::where('name', $itemName)->first();
                $size = $item?->sizes()->where('size', $itemSize)->first();

                if (!$warehouse) $errors[] = 'Invalid Warehouse ID';
                if (!$item) $errors[]      = 'Invalid Item Name';
                if ($item && !$size) $errors[] = 'Invalid Item Size for given Item';
            }

            if (!empty($errors)) {
                $this->skippedRows[] = [
                    'row'   => $rowNumber,
                    'name'  => $itemName ?: 'N/A',
                    'error' => implode(', ', $errors),
                ];
                $rowNumber++;
                continue;
            }

            // ✅ Passed all checks → Add to valid rows
            $validRows[] = [
                'item_id'      => $item->id,
                'size_id'      => $size->id,
                'length'       => $length,
                'warehouse_id' => $warehouseId,  // add warehouse for transaction
                'quantity'     => intval($quantity), // save actual quantity
                'sunil_sponge' => $warehouseId == 1 ? intval($quantity) : 0,
                'sunil_steel'  => $warehouseId != 1 ? intval($quantity) : 0,
            ];


            $rowNumber++;
        }

        // ❌ If any error exists, skip all inserts
        if (!empty($this->skippedRows)) {
            Session::flash('import_errors', $this->skippedRows);
            return;
        }

        // ✅ Save only if no errors at all
        DB::transaction(function () use ($validRows) {
            foreach ($validRows as $data) {
                $stock = Stock::where('item_id', $data['item_id'])
                    ->where('size_id', $data['size_id'])
                    ->where('length', $data['length'])
                    ->first();

                if ($stock) {
                    if ($data['warehouse_id'] == 1) {
                        $stock->sunil_sponge += $data['quantity'];
                    } else {
                        $stock->sunil_steel += $data['quantity'];
                    }
                    $stock->save();
                } else {
                    Stock::create($data);
                }

                // ✅ Record stock transaction
                StockTransactionService::record([
                    'user_id'      => auth()->id(),
                    'warehouse_id' => $data['warehouse_id'],
                    'customer_id'  => null,
                    'refrence_id'  => null,
                    'item_id'      => $data['item_id'],
                    'size_id'      => $data['size_id'],
                    'length'       => $data['length'],
                    'quantity'     => $data['quantity'],
                    'type'         => 'Add Stock',
                    'operation'    => 'Addition',
                ]);

                $this->changed = true;
            }
        });
    }
}
