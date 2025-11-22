<?php

namespace App\Imports;

use App\Models\ItemBasicPrice;
use App\Models\ItemName;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ItemNameImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];
    protected array $updatedGroups = []; // groups already processed
    protected array $validRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $group = trim($row['item_group'] ?? '');
            $price = $row['basic_price'] ?? null;
            $remark = $row['remark'] ?? null;

            // Duplicate check in import file
            if (in_array($group, $this->updatedGroups)) {
                $this->addError($index, $group, 'Duplicate group in import file.');
                continue;
            }

            // Price validation
            if (
                $price === null ||
                !is_numeric($price) ||
                floatval($price) <= 0
            ) {
                $this->addError($index, $group, 'Price is not correct.');
                continue;
            }

            // Fetch all items under this group
            $items = ItemName::where('item_group', $group)->get();

            if ($items->isEmpty()) {
                $this->addError($index, $group, 'Item group does not exist in DB.');
                continue;
            }

            // Check for pending price update on ANY item in this group
            $hasPending = ItemBasicPrice::whereIn('item_name_id', $items->pluck('id'))
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                $this->addError($index, $group, 'Previous pending price update exists for this group. Please approve it first.');
                continue;
            }

            // Collect valid rows (whole group)
            $this->validRows[] = [
                'items'  => $items,
                'group'  => $group,
                'price'  => $price,
                'remark' => $remark,
            ];

            $this->updatedGroups[] = $group;
        }

        // ❌ Stop if any error
        if (!empty($this->errors)) {
            return;
        }

        // ✅ Insert for all items in all groups
        foreach ($this->validRows as $row) {
            foreach ($row['items'] as $item) {
                ItemBasicPrice::create([
                    'user_id'      => Auth::id(),
                    'item_name_id' => $item->id,
                    'old_price'    => $item->price,
                    'new_price'    => $row['price'],
                    'remark'       => $row['remark'],
                ]);
            }
        }
    }

    private function addError($index, $group, $message): void
    {
        $this->errors[] = [
            'row'   => $index + 2, // +2 because heading + 1-based index
            'group' => $group,
            'error' => $message,
        ];
    }
}
