<?php

namespace App\Imports;

use App\Models\ItemSize;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Database\QueryException;

class ItemSizeImport implements ToCollection
{
    public array $errors = [];

    public function collection(Collection $rows)
    {
        // Skip header row
        $rows->shift();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $data = [
                'id'     => $row[0],
                'rate'  => $row[5],
                'remark' => $row[6] ?? null,
            ];

            // 1. Validate
            $validator = Validator::make($data, [
                'id'     => 'required|exists:item_sizes,id',
                'rate'  => 'required|numeric',
                'remark' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row'    => $rowNumber,
                    'errors' => $validator->errors()->all(),
                    'data'   => $data,
                ];
                continue;
            }

            // 2. Try DB update safely
            try {
                ItemSize::where('id', $data['id'])->update([
                    'price'  => $data['rate'],
                    'remark' => $data['remark'],
                ]);
            } catch (QueryException $e) {
                // Capture SQL error (e.g. numeric overflow)
                $this->errors[] = [
                    'row'    => $rowNumber,
                    'errors' => ['Database error: ' . $e->getMessage()],
                    'data'   => $data,
                ];
            }
        }
    }
}
