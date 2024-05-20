<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 
        'price',
        'quantity',
        'total_tax',
        'invoice_id',
        'inventory_item_id'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
