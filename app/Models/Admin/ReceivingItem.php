<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivingItem extends Model
{
    use SoftDeletes;

    protected $table = 'receiving_items';
    public $timestamps = true;

    protected $fillable = [
        'receiving_id',
        'order_item_id',
        'product_sku',
        'product_name',
        'product_barcode',
        'supplier_id',
        'brand_id',
        'ordered_qty',
        'received_qty',
        'notes',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function batches()
    {
        return $this->hasMany(ReceivingBatch::class);
    }

}
