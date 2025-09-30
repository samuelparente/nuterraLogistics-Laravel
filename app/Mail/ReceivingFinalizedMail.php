<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceivingFinalizedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $summary;   // fornecedor, marcas, metas
    public array $divergences; // itens com diferença entre encomendado e recebido
    public array $newItems;    // itens assinalados is_new=1

    public function __construct(array $summary, array $divergences, array $newItems)
    {
        $this->summary     = $summary;
        $this->divergences = $divergences;
        $this->newItems    = $newItems;
    }

    public function build()
    {
        return $this->subject('RECEPÇÃO CONCLUÍDA — ' . ($this->summary['supplier_name'] ?? 'Fornecedor'))
                    ->markdown('emails.receivings.finalized')
                    ->with([
                        'summary'     => $this->summary,
                        'divergences' => $this->divergences,
                        'newItems'    => $this->newItems,
                    ]);
    }
}
