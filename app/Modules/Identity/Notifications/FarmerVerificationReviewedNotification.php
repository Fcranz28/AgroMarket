<?php

namespace App\Modules\Identity\Notifications;

use App\Modules\Identity\Enums\VerificationStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Result of the identity review of a farmer account.
 */
class FarmerVerificationReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VerificationStatus $status) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === VerificationStatus::Approved) {
            return (new MailMessage)
                ->subject('¡Tu cuenta de agricultor fue aprobada!')
                ->greeting('¡Bienvenido a AgroMarket!')
                ->line('Revisamos tus documentos y ya puedes publicar tus productos.')
                ->action('Publicar un producto', route('dashboard.productos.create'));
        }

        return (new MailMessage)
            ->subject('No pudimos verificar tu cuenta de agricultor')
            ->line('Revisamos tus documentos pero no pudimos validar tu identidad.')
            ->line('Responde a este correo si crees que se trata de un error o para enviar nuevas fotos.');
    }
}
