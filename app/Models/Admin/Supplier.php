<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = ['erp_id', 'name', 'notes'];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
