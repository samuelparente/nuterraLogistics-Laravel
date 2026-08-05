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
        'fx_currency',
        'fx_rate_to_eur',
        'fx_rate_at',
        'fx_source_unit_price',
        'fx_unit_price_eur',
        'notes',
        'is_new', 
    ];

     protected $casts = [
        'is_new' => 'boolean',
        'fx_rate_to_eur' => 'decimal:12',
        'fx_rate_at' => 'datetime',
        'fx_source_unit_price' => 'decimal:6',
        'fx_unit_price_eur' => 'decimal:6',
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
 
    public function isNew(): bool
    {
        return (bool) $this->is_new;
    }
}
