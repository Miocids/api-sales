<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;


class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
    * @property Model $_user;
    */
    private Model $_user;

    /**
    * @property string $_token
    */
    private string $_token;


    /**
     * Create a new message instance.
     */
    public function __construct(Model $user, string $token)
    {
        $this->_user  = $user;
        $this->_token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reestablecer Contraseña',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.code',
            with: [
                'name'      => $this->_user->name,
                'code'      => $this->_token,
            ],

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
