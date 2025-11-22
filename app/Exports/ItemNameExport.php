<?php

namespace App\Exports;

use App\Models\ItemName;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class ItemNameExport implements FromCollection, WithHeadings, WithEvents
{
    protected $exportedData;

    public function collection()
    {
        $this->exportedData = ItemName::select('item_group')
            ->selectRaw('MIN(price) as price, MIN(remark) as remark')
            ->groupBy('item_group')
            ->get()
            ->map(function ($row, $index) {
                return [
                    'S No.'        => $index + 1,
                    'Item Group'   => $row->item_group,
                    'Basic Price'  => $row->price ?? 0,
                    'Remark'       => "",
                ];
            });

        return $this->exportedData;
    }

    public function headings(): array
    {
        return ['S. No.', 'Item Group', 'Basic Price', 'Remark'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Total rows (1 heading + N data rows)
                $lastRow = ($this->exportedData->count() ?? 0) + 1;

                // 1. Unlock all cells (A1:D{lastRow})
                $sheet->getStyle("A1:D{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

                // 2. Lock heading row completely
                $sheet->getStyle("A1:D1")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // 3. Lock specific data columns (A–B rows 2..N)
                $sheet->getStyle("A2:B{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // 4. Bold heading row
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);

                // 5. Auto-size A to C
                foreach (range('A', 'C') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // 6. Set custom width for D
                $sheet->getColumnDimension('D')->setWidth(40);

                // 7. Enable sheet protection
                $sheet->getProtection()->setSheet(true);
                // Optional: password protection
                // $sheet->getProtection()->setPassword('1234');

                // 8. Add Numeric Validation for Basic Price (Column C)
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell("C{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_WHOLE);
                    $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Invalid Input');
                    $validation->setError('Only numeric values are allowed in Basic Price.');
                    $validation->setPromptTitle('Numeric Input Required');
                    $validation->setPrompt('Please enter a valid number.');
                    $validation->setFormula1(0); // allow >=0
                }
            }
        ];
    }
}
