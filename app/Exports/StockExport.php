<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\ItemName;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StockExport implements FromArray, WithHeadings, WithEvents
{
    protected $rows = [];
    protected $warehouse;
    protected $warehouseList;

    public function __construct($warehouseId)
    {
        $this->warehouse = Warehouse::find($warehouseId);
        $this->warehouseList = Warehouse::pluck('prefix')->toArray();

        $items = ItemName::with('sizes')->get();
        $serial = 1;

        foreach ($items as $item) {
            foreach ($item->sizes as $size) {
                $this->rows[] = [
                    $serial++,
                    $this->warehouse?->prefix ?? '',
                    $this->warehouse?->id ?? '',
                    $item->name,
                    $size->size,
                    '',       // Length
                    '0',       // Quantity
                    // '',        // Remark (blank by default)
                ];
            }
        }
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['S. No', 'Warehouse', 'Warehouse ID', 'Item Name', 'Item Size', 'Length', 'Quantity'];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->rows);
                $startRow = 2; // Start from row 2 (skip headings)
                $endRow = $rowCount + 1;

                // for ($r = $startRow; $r <= $endRow; $r++) {
                //     // Quantity must be a number – Column G
                //     $qtyValidation = $sheet->getCell("G$r")->getDataValidation();
                //     $qtyValidation->setType(DataValidation::TYPE_DECIMAL);
                //     $qtyValidation->setErrorStyle(DataValidation::STYLE_STOP);
                //     $qtyValidation->setAllowBlank(true);
                //     $qtyValidation->setShowInputMessage(true);
                //     $qtyValidation->setShowErrorMessage(true);
                //     $qtyValidation->setErrorTitle('Invalid Quantity');
                //     $qtyValidation->setError('Only numbers allowed in Quantity.');
                //     $qtyValidation->setFormula1(0);
                // }
                for ($r = $startRow; $r <= $endRow; $r++) {
                    // Quantity must be an integer >= 0 – Column G
                    $qtyValidation = $sheet->getCell("G$r")->getDataValidation();
                    $qtyValidation->setType(DataValidation::TYPE_WHOLE);
                    $qtyValidation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                    $qtyValidation->setErrorStyle(DataValidation::STYLE_STOP);
                    $qtyValidation->setAllowBlank(true);
                    $qtyValidation->setShowInputMessage(true);
                    $qtyValidation->setShowErrorMessage(true);
                    $qtyValidation->setErrorTitle('Invalid Quantity');
                    $qtyValidation->setError('Only positive numbers allowed in Quantity.');
                    $qtyValidation->setFormula1('0'); // minimum value = 0
                }

                // Lock entire sheet first
                $sheet->getProtection()->setSheet(true);

                // Unlock Length (F) and Quantity (G) for rows starting from 2
                $sheet->getStyle("F{$startRow}:F{$endRow}")
                    ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $sheet->getStyle("G{$startRow}:G{$endRow}")
                    ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Set column widths & left align
                foreach (range('A', 'G') as $col) {
                    $sheet->getStyle("{$col}{$startRow}:{$col}{$endRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    if (!in_array($col, ['A'])) {
                        $sheet->getColumnDimension($col)->setWidth(15);
                    }
                }
            },
        ];
    }
}
