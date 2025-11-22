<?php

namespace App\Services;

use App\Models\StockTransaction;

class StockTransactionService
{
    public static function record(array $data): StockTransaction
    {
        return StockTransaction::create([
            'user_id'      => $data['user_id'],
            'warehouse_id' => $data['warehouse_id'],
            'customer_id'  => $data['customer_id'] ?? null,
            'refrence_id'  => $data['refrence_id'] ?? null,
            'item_id'      => $data['item_id'],
            'size_id'      => $data['size_id'],
            'length'       => $data['length'],
            'quantity'     => $data['quantity'],
            'type'         => $data['type'],
            'operation'    => $data['operation'],
        ]);
    }
}
