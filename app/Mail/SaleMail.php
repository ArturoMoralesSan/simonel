<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SaleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct($sale, $pdfPath)
    {
        $this->sale = $sale;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Cotización de Productos ordenada')
                ->view('emails.saleordered') // Usar vista Blade en lugar de markdown
                ->attach($this->pdfPath, [
                    'as' => 'cotizacion.pdf',
                    'mime' => 'application/pdf',
                ]);
    }
}
