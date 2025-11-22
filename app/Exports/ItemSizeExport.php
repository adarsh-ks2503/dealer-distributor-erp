<?php

namespace App\Exports;

use App\Models\ItemSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ItemSizeExport implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ItemSize::with(['item_name' => function ($query) {
            $query->withTrashed();
        }])->withTrashed()->get()
            ->map(function ($row, $index) {
                return [
                    'S No.' => $index + 1,
                    'Item Size Id' => $row->id,
                    'Item Name' => $row->item_name->name ?? 'N/A',
                    'Size' => $row->size,
                    'Weight' => $row->weight,
                    'Rate' => $row->price,
                    'Remark' => $row->remark ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return ['S. No.', 'Item Size Id', 'Item Name', 'Size', 'Weight', 'Rate', 'Remark'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = ItemSize::count() + 1;

                // Lock all cells
                $sheet->getStyle("A1:G{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // Unlock only F and G (Rate and Remark)
                $sheet->getStyle("F2:G{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Bold header
                $sheet->getStyle('A1:G1')->getFont()->setBold(true);

                // Auto-size all except F
                foreach (range('A', 'G') as $col) {
                    if ($col !== 'F') {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }
                }

                // Set custom width for column F (Rate)
                $sheet->getColumnDimension('F')->setWidth(20);

                // Set number format for 2 decimals in Rate
                $sheet->getStyle("F2:F{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // ✅ Apply DataValidation to restrict to numbers only with up to 2 decimals
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell("F{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_DECIMAL);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Invalid Rate');
                    $validation->setError('Please enter a number with max 2 decimal places only.');
                    $validation->setPromptTitle('Rate Field');
                    $validation->setPrompt('Only numbers with 2 decimal places are allowed.');
                    $validation->setFormula1(0);                 // Minimum
                    $validation->setFormula2(999999999);         // Maximum
                }

                // ✅ Sheet protection MUST be on for validation to work!
                $sheet->getProtection()->setSheet(true);
            },
        ];
    }
}
