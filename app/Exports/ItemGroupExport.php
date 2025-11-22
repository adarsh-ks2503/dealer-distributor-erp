<?php

namespace App\Exports;

use App\Models\ItemMaster;
use Maatwebsite\Excel\Concerns\FromCollection;

class ItemGroupExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ItemSize::get()
            ->map(function ($row, $index) {
                $date = date('d-M-Y'); // use correct date format (year-month-day)

                return [
                    'S No.' => $index + 1,
                    'Item Name' => $row->name,
                    'Basic Price' => 39000, // static or dynamic if needed
                    'Date' => $date,
                    'Remark' => '',
                ];
            });
    }

    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return ['S. No.', 'Item Name', 'Basic Price', 'Date', 'Remark'];
    }
}
