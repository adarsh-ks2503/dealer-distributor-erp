<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\State;
use App\Models\ItemBasicPrice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemBasicPriceImport implements ToCollection, WithHeadingRow
{
    public $errors = [];

    public function collection(Collection $rows)
    {
        $pendingConflicts = [];
        $validRows = [];

        // First pass: Validate all rows and collect errors
        foreach ($rows as $index => $row) {
            $sno = $row['s_no'] ?? ($index + 1); // Use S No. from Excel or row number
            $item_name = trim($row['item'] ?? '');
            $state_name = trim($row['state'] ?? '');
            $market_basic_price = $row['market_basic_price'] ?? '';
            $distributor_basic_price = $row['distributor_basic_price'] ?? '';
            $dealer_basic_price = $row['dealer_basic_price'] ?? '';

            // Skip rows where all price fields are empty without adding an error
            if (empty($market_basic_price) && empty($distributor_basic_price) && empty($dealer_basic_price)) {
                continue;
            }

            // Validate required fields
            if (empty($item_name) || empty($state_name)) {
                $this->errors[] = "Row {$sno}: Item and Region are required.";
                continue;
            }

            // Validate prices are non-negative numbers
            if (!is_numeric($market_basic_price) || $market_basic_price < 0 ||
                !is_numeric($distributor_basic_price) || $distributor_basic_price < 0 ||
                !is_numeric($dealer_basic_price) || $dealer_basic_price < 0) {
                $this->errors[] = "Row {$state_name}: All three basic prices are required and must be non-negative numbers.";
                continue;
            }

            // Find item by name
            $item = Item::where('item_name', $item_name)->first();
            if (!$item) {
                $this->errors[] = "Row {$sno}: Invalid Item '{$item_name}'.";
                continue;
            }

            // Find state by name
            $state = State::where('state', $state_name)->first();
            if (!$state) {
                $this->errors[] = "Row {$sno}: Invalid Region '{$state_name}'.";
                continue;
            }

            // Get IDs
            $item_id = $item->id;
            $state_id = $state->id;

            // Check for existing pending request
            $pendingExists = ItemBasicPrice::where('item', $item_id)
                ->where('region', $state_id)
                ->where('status', 'Pending')
                ->exists();

            if ($pendingExists) {
                $pendingConflicts[] = "{$item_name} - {$state_name}";
                continue;
            }

            // Store valid row data for second pass
            $validRows[] = [
                'item_id' => $item_id,
                'state_id' => $state_id,
                'market_basic_price' => (int) $market_basic_price,
                'distributor_basic_price' => (int) $distributor_basic_price,
                'dealer_basic_price' => (int) $dealer_basic_price,
            ];
        }

        // Add conflict message if any
        if (!empty($pendingConflicts)) {
            $this->errors[] = "Pending requests already exist for: " . implode(', ', $pendingConflicts) . ". Please approve or reject these before submitting new requests.";
        }

        // If there are any errors, stop processing and return
        if (!empty($this->errors)) {
            return;
        }

        // Check if no valid rows were found
        if (empty($validRows)) {
            $this->errors[] = "No valid data found in the Excel file. Please ensure the file contains valid rows with required data.";
            return;
        }

        // Second pass: Create records only if no errors were found
        foreach ($validRows as $row) {
            ItemBasicPrice::create([
                'item' => $row['item_id'],
                'region' => $row['state_id'],
                'market_basic_price' => $row['market_basic_price'],
                'distributor_basic_price' => $row['distributor_basic_price'],
                'dealer_basic_price' => $row['dealer_basic_price'],
                'remarks' => '', // No remarks in Excel
                'status' => 'Pending',
                'approval_date' => null,
                'approved_by' => null,
            ]);
        }
    }
}
