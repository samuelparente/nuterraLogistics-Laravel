<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = ['erp_id', 'name', 'notes', 'supplier_id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
