<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receiving extends Model
{
    use SoftDeletes;

    protected $table = 'receivings';
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'supplier_id',
        'status_id', // <--- ADICIONADO
        'started_at',
        'received_at',
        'received_by',
        'notes',
        'files',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'received_at' => 'datetime',
        'files' => 'array',
    ];

    // Relações
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(ReceivingItem::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function status() // <--- ADICIONADO
    {
        return $this->belongsTo(Status::class);
    }
}
