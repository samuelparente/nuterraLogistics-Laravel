<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_sku',
        'product_barcode',
        'product_name',
        'quantity',
        'brand_id',
        'supplier_id',
  
    ];

   

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

   
}
