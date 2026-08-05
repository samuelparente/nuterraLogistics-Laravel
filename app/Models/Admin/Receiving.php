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
        'fx_currency',
        'fx_rate_to_eur',
        'fx_preview',
        'fx_preview_token',
        'fx_previewed_at',
        'erp_submission_status',
        'erp_submission_key',
        'erp_document_reference',
        'erp_response',
        'erp_submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'received_at' => 'datetime',
        'files' => 'array',
        'fx_rate_to_eur' => 'decimal:12',
        'fx_preview' => 'array',
        'fx_previewed_at' => 'datetime',
        'erp_response' => 'array',
        'erp_submitted_at' => 'datetime',
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

    public function status() 
    {
        return $this->belongsTo(Status::class);
    }

    public function hasDivergences(): bool
    {
        foreach ($this->items as $item) {
            // Produto extra (não faz parte da order original)
            if (is_null($item->order_item_id)) {
                return true;
            }

            // Produto com quantidade a mais ou a menos
            if ($item->received_qty != $item->ordered_qty) {
                return true;
            }
        }

        return false;
    }

}
