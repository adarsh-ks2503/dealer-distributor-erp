<?php

namespace App\Exports;

use App\Models\State;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ItemBasicPriceExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Fetch all states from the database, sorted alphabetically
        $states = State::orderBy('state')->pluck('state')->toArray();

        // Create one row per state, pre-filling Item and State
        return collect($states)->map(function ($state, $index) {
            return [
                'S No.' => $index + 1,
                'Item' => 'SINGHAL TMT', // Hardcoded; adjust if dynamic items needed
                'State' => $state,
                'Market Basic Price' => '',
                'Distributor Basic Price' => '',
                'Dealer Basic Price' => '',
                'Status' => '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'S. No.',
            'Item',
            'State',
            'Market Basic Price',
            'Distributor Basic Price',
            'Dealer Basic Price',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count(State::pluck('state')->toArray()) + 1; // Header + one row per state

                // Unlock all cells initially
                $sheet->getStyle("A1:G{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Lock header row and S No. column
                $sheet->getStyle("A1:G1")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle("A2:A{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);

                // Lock Item, State, and Status columns to prevent editing
                $sheet->getStyle("B2:B{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle("C2:C{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                $sheet->getStyle("G2:G{$lastRow}")->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);

                // Bold headings
                $sheet->getStyle('A1:G1')->getFont()->setBold(true);

                // Auto-size columns A to G
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Enable sheet protection
                $sheet->getProtection()->setSheet(true);

                // Numeric validation for price columns (D to F)
                foreach (['D', 'E', 'F'] as $col) {
                    $priceValidation = $sheet->getDataValidation("{$col}2:{$col}{$lastRow}");
                    $priceValidation->setType(DataValidation::TYPE_WHOLE);
                    $priceValidation->setErrorStyle(DataValidation::STYLE_STOP);
                    $priceValidation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                    $priceValidation->setAllowBlank(true);
                    $priceValidation->setShowInputMessage(true);
                    $priceValidation->setShowErrorMessage(true);
                    $priceValidation->setErrorTitle('Invalid Price');
                    $priceValidation->setError('Please enter a non-negative integer.');
                    $priceValidation->setPromptTitle('Enter Price');
                    $priceValidation->setPrompt('Enter a non-negative integer.');
                    $priceValidation->setFormula1(0);
                }

                // Set formula for Status column (G)
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->setCellValue(
                        "G{$row}",
                        '=IF(COUNTA(D' . $row . ':F' . $row . ')=0, "", IF(COUNTA(D' . $row . ':F' . $row . ')=3, "OK", "Error: Must fill all three prices or none"))'
                    );
                }

                // Conditional formatting to highlight partial fills in red
                $partialCondition = new Conditional();
                $partialCondition->setConditionType(Conditional::CONDITION_EXPRESSION);
                $partialCondition->addCondition('AND(COUNTA($D2:$F2)>0, COUNTA($D2:$F2)<3)');
                $partialCondition->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000'); // Red background
                $partialCondition->getStyle()->getFont()->setColor(new Color('FFFFFFFF')); // White text

                $conditionalStyles = $sheet->getStyle("D2:F{$lastRow}")->getConditionalStyles();
                $conditionalStyles[] = $partialCondition;
                $sheet->getStyle("D2:F{$lastRow}")->setConditionalStyles($conditionalStyles);
            },
        ];
    }
}
