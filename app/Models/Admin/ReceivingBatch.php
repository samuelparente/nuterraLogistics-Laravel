<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivingBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiving_item_id',
        'batch_number',
        'expiry_date',
        'quantity',
    ];

    public function receivingItem()
    {
        return $this->belongsTo(ReceivingItem::class);
    }
}
