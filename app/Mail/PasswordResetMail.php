<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $email;
    public $expiresAt;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetUrl, $email, $expiresAt)
    {
        $this->resetUrl = $resetUrl;
        $this->email = $email;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Đặt lại mật khẩu - SokishiX Chat',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'resetUrl' => $this->resetUrl,
                'email' => $this->email,
                'expiresAt' => $this->expiresAt,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
