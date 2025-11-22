<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemSize extends Model
{
    use HasFactory;
    protected $table = 'item_sizes';

    protected $guarded = [];

    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item');
    }

}
