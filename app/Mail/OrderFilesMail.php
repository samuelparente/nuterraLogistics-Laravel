<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderFilesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadLinks;
    protected $fileAttachments;

    public function __construct(array $attachments, array $downloadLinks)
    {
        $this->fileAttachments = $attachments;
        $this->downloadLinks = $downloadLinks;
    }

    public function build()
    {
        $mail = $this->subject('Novo Pedido de Encomendas a Fornecedores')
                     ->markdown('emails.orders.files')
                     ->with([
                         'downloadLinks' => $this->downloadLinks,
                     ]);

        foreach ($this->fileAttachments as $file) {
            $mail->attachFromStorageDisk('public', $file['path'], $file['filename']);
        }

        return $mail;
    }
}
