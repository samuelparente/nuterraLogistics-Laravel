<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceivingFinalizedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $summary;      // fornecedor, marcas, metas
    public array $divergences;  // itens com diferença entre encomendado e recebido
    public array $newItems;     // itens assinalados is_new=1
    public ?string $attachmentPath; // caminho relativo no disk 'public'

    public function __construct(
        array $summary,
        array $divergences,
        array $newItems,
        ?string $attachmentPath = null
    ) {
        $this->summary        = $summary;
        $this->divergences    = $divergences;
        $this->newItems       = $newItems;
        $this->attachmentPath = $attachmentPath;
    }

    public function build()
    {
        $email = $this->subject('RECEPÇÃO CONCLUÍDA — ' . ($this->summary['supplier_name'] ?? 'Fornecedor'))
            ->markdown('emails.receivings.finalized')
            ->with([
                'summary'     => $this->summary,
                'divergences' => $this->divergences,
                'newItems'    => $this->newItems,
            ]);

        // Anexa o Excel, se existir caminho
        if (!empty($this->attachmentPath)) {
            $email->attachFromStorageDisk('public', $this->attachmentPath);
            // Se quiseres dar nome fixo:
            // $email->attachFromStorageDisk('public', $this->attachmentPath, 'rececao_detalhada.xlsx');
        }

        return $email;
    }
}
