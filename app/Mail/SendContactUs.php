<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendContactUs extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public function __construct($data)
    {
        //
        $this->data = $data;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Inquiry Received',
        );
    }
    /**
     * Get the message envelope.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_us', // Update to your actual view path
            with: ['data' => $this->data], // Pass data to the view
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
