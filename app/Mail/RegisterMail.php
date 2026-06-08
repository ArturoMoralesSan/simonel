<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    { 
        return $this->subject('¡Bienvenido a Atvantag3! accedde a tu cuenta')
                    ->view('emails.registrado')
                    ->with([
                        'user' => $this->user, // Asegúrate de que esta línea pase los datos correctos.
                    ]);;
    }
}
