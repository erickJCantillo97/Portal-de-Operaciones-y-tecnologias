<?php

namespace App\Notifications;

use App\Models\WareHouse\Tool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentToolNotification extends Notification
{
    // use Queueable;

    protected $employee;

    protected $tools;

    protected $subject;

    /**
     * Create a new notification instance.
     */
    public function __construct($employee, $tools, $subject = null)
    {
        $this->employee = $employee;
        $this->tools = Tool::whereIn('id', $tools)->get();
        $this->subject = $subject;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->subject)
                    ->view('emails/assignmentEmail', [
                        'employee' => $this->employee,
                        'tools' => $this->tools
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
