<?php

namespace App\Mail;

use App\Models\WareHouse\Tool;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignmentToolsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $employee;

    public $tools;

    // public $theme = "assignmentsTools.css";

    // public $theme = 'assignmentsTools';



    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($employee, $tools = [0])
    {
        $this->employee = $employee;
        $this->tools = Tool::whereIn('id', $tools)->get();
    }

    // public function __construct($employee, $tools, $subject = null)
    // {
    //     $this->employee = $employee;
    //     $this->tools = Tool::whereIn('id', $tools)->get();
    //     $this->subject = $subject;
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // subject: $this->subject,
            subject: 'Nueva Asiganacion de Equipos',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.assignment-tools-mail',
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
