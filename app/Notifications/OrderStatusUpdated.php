<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order, public string $oldStatus)
    {
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
        $statusLabel = $this->getStatusLabel($this->order->status);
        $message = $this->getStatusMessage($this->order->status);

        return (new MailMessage)
            ->subject("Mise à jour de votre commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Le statut de votre commande a été mis à jour.")
            ->line("**Nouveau statut :** {$statusLabel}")
            ->line($message)
            ->action('Voir ma commande', route('orders.show', $this->order))
            ->salutation('L\'équipe Dropshipping Tunisia');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
        ];
    }

    /**
     * Get status label in French
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }

    /**
     * Get contextual message for status
     */
    private function getStatusMessage(string $status): string
    {
        return match($status) {
            'confirmed' => 'Votre commande a été confirmée et est en cours de préparation.',
            'processing' => 'Votre commande est actuellement en cours de traitement par nos fournisseurs.',
            'shipped' => 'Votre commande a été expédiée ! Vous recevrez bientôt les détails de suivi.',
            'delivered' => 'Votre commande a été livrée avec succès ! Merci de votre confiance.',
            'cancelled' => 'Votre commande a été annulée. Si vous avez des questions, contactez notre support.',
            default => 'Votre commande a été mise à jour.',
        };
    }
}
