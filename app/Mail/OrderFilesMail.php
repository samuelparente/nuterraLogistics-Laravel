<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderFilesMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array Lista de nomes de ficheiros (opcional, não será mostrada na view nova) */
    public array $downloadLinks;

    /** @var array Resumo do pedido: ['suppliers' => [[supplier, brands[], lines]], 'count' => int] */
    public array $orderSummary;

    /** @var array Registos dos anexos: [['path' => ..., 'filename' => ..., ...], ...] */
    protected array $fileAttachments;

    /**
     * @param array $attachments   Registos para anexar (disk 'public')
     * @param array $downloadLinks (opcional) nomes dos ficheiros
     * @param array $orderSummary  (opcional) resumo por fornecedor/marcas
     */
    public function __construct(array $attachments, array $downloadLinks = [], array $orderSummary = [])
    {
        $this->fileAttachments = $attachments;
        $this->downloadLinks   = $downloadLinks;
        $this->orderSummary    = $orderSummary;
    }

    public function build()
    {
        $mail = $this->subject('PEDIDOS A FORNECEDORES | Novo pedido')
                     ->markdown('emails.orders.new') // <- vamos criar já a seguir
                     ->with([
                         'orderSummary' => $this->orderSummary,
                         // 'downloadLinks' => $this->downloadLinks, // só se quiseres usar na view
                     ]);

        foreach ($this->fileAttachments as $file) {
            // Garante que existem as chaves antes de anexar
            if (!empty($file['path']) && !empty($file['filename'])) {
                $mail->attachFromStorageDisk('public', $file['path'], $file['filename']);
            }
        }

        return $mail;
    }
}
