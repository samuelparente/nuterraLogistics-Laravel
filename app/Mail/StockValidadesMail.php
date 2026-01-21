<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockValidadesMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $attachmentPath;
    public int $totalLinhas;

    /** opcional (para texto bonito no email) */
    public ?int $monthsAhead;
    public int $redDays;
    public int $yellowDays;

    public function __construct(
        string $attachmentPath,
        int $totalLinhas,
        ?int $monthsAhead = null,
        int $redDays = 60,
        int $yellowDays = 60
    ) {
        $this->attachmentPath = $attachmentPath;
        $this->totalLinhas    = $totalLinhas;
        $this->monthsAhead    = $monthsAhead;
        $this->redDays        = $redDays;
        $this->yellowDays     = $yellowDays;
    }

    public function build()
    {
        $mail = $this->subject('RELATÓRIO DIÁRIO DE VALIDADES — NUTERRA | logistics')
            ->view('emails.reports.stock_validades')
            ->with([
                'totalLinhas' => $this->totalLinhas,
                'monthsAhead' => $this->monthsAhead,
                'redDays'     => $this->redDays,
                'yellowDays'  => $this->yellowDays,
            ]);

        if (!empty($this->attachmentPath)) {
            $mail->attachFromStorageDisk('public', $this->attachmentPath);
        }

        return $mail;
    }
}
