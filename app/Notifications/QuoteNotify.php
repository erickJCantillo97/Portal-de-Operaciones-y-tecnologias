<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteVersion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteNotify extends Notification
{
    use Queueable;

    protected $quote;
    protected $name;
    protected $type;
    /**
     * Create a new notification instance.
     */
    public function __construct($name, QuoteVersion $quote, string $type)
    {
        // dd('llega');
        $this->quote = $quote;
        $this->name = $name;
        $this->type = $type;
        //
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

        if ($this->type == 'asignament') {
            return (new MailMessage)
                ->subject('Nueva estimacion asignada')
                ->greeting('¡Hola! ' . $this->name)
                ->line('Se le ha asignado la estimacion #' . $this->quote->consecutive
                    . ' para su realización, haga clic en el botón para ingresar a TOP y completarla. Use su usuario de Windows y la contraseña de su usuario de Windows')
                ->action('Ir a estimacion', route('quotes.index'))

                ->line('¡Gracias por usar la aplicacion!')
                ->salutation('¡Saludos!');
        } else if ($this->type == 'response') {
            return (new MailMessage)
                ->subject('Estimacion enviada para aprobacion')
                ->greeting('¡Hola! ' . $this->name)
                ->line('Se ha enviado la estimacion #' . $this->quote->consecutive
                    . ' para su aprobacion, de clic en el botón para ingresar a TOP para verificarla. Use su usuario de Windows y la contraseña de su usuario de Windows')
                ->action('Ir a la estimacion', url(env('APP_URL', 'http://localhost')))
                // ->attach('ruta/al/archivo')
                ->line('¡Gracias por usar la aplicacion!')
                ->salutation('¡Saludos!');
        } else {
            return (new MailMessage)
                ->subject('Error');
        }
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
