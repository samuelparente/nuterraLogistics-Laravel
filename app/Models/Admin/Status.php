<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'label_pt',
        'color',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
