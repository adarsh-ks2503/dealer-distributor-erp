<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HasAutoCrud
{
    public function generateNumber($modelClass, $prefix, $column = 'number_column')
    {
        $year = date('Y');
        $month = date('m');
        $fy_start = $month >= 4 ? $year : $year - 1;
        $fy_end = $month >= 4 ? $year + 1 : $year;
        $fy_short = substr($fy_start, -2) . substr($fy_end, -2);

        $last = $modelClass::whereBetween('created_at', [
            "$fy_start-04-01 00:00:00",
            "$fy_end-03-31 23:59:59"
        ])->latest('id')->first();

        $next_serial = $last
            ? str_pad(((int) substr($last->{$column}, -4)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return $prefix . $fy_short . '_' . $next_serial;
    }

    protected function mapFieldsFromRequest(Request $request, array $fieldMap, $index = null)
    {
        $data = [];
        foreach ($fieldMap as $dbField => $reqField) {
            $data[$dbField] = is_null($index) ? $request->$reqField : $request->$reqField[$index] ?? null;
        }
        return $data;
    }

    public function storeCommon(Request $request)
    {
        $mainModel = $this->fieldMap['main_model'];
        $itemModel = $this->fieldMap['item_model'];
        $attachmentModel = $this->fieldMap['attachment_model'];
        $numberCol = $this->fieldMap['number_column'];
        $prefix = $this->fieldMap['prefix'];

        // Generate number
        $number = $this->generateNumber($mainModel, $prefix, $numberCol);

        // Map main fields and save
        $mainData = $this->mapFieldsFromRequest($request, $this->fieldMap['main_fields']);
        $mainData[$numberCol] = $number;
        $mainData['status'] = 'pending'; // Default status
        // ✅ Conditionally add total_products only for Enquiry model
        if (in_array('total_products', (new $mainModel)->getFillable())) {
            $mainData['total_products'] = count($request->size_id ?? []);
        }
        $main = $mainModel::create($mainData);

        // Map and save items
        if (isset($request->item_id) && is_array($request->item_id)) {
            foreach ($request->item_id as $index => $item_id) {
                $itemData = $this->mapFieldsFromRequest($request, $this->fieldMap['item_fields'], $index);
                $itemData['column_id'] = $main->id;
                $itemModel::create($itemData);
            }
        }

        // Map and save attachments
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $index => $file) {
                $attachmentData = $this->mapFieldsFromRequest($request, $this->fieldMap['attachment_fields'], $index);
                $attachmentData['column_id'] = $main->id;
                $attachmentData['file_path'] = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
                $attachmentData['file_name'] = $file->getClientOriginalName();
                $attachmentModel::create($attachmentData);
            }
        }

        return $main;
    }
}
