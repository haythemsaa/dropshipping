<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
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
        $this->order->load('items.variant');

        $message = (new MailMessage)
            ->subject("Confirmation de commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Merci pour votre commande ! Nous avons bien reçu votre commande #{$this->order->order_number}.")
            ->line("**Récapitulatif de votre commande :**");

        // Ajouter les articles
        foreach ($this->order->items as $item) {
            $itemLine = "- {$item->getDisplayName()} (Qté: {$item->quantity}) - {$item->subtotal} DT";
            $message->line($itemLine);
        }

        $message->line("**Montant total : {$this->order->total_amount} DT**")
            ->line("Statut : **" . $this->getStatusLabel($this->order->status) . "**")
            ->line("Mode de paiement : **" . $this->getPaymentMethodLabel($this->order->payment_method) . "**")
            ->action('Voir ma commande', route('orders.show', $this->order))
            ->line("Vous recevrez une notification dès que votre commande sera expédiée.")
            ->line("Merci de votre confiance !")
            ->salutation('L\'équipe Dropshipping Tunisia');

        return $message;
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
            'total_amount' => $this->order->total_amount,
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
     * Get payment method label in French
     */
    private function getPaymentMethodLabel(string $method): string
    {
        return match($method) {
            'card' => 'Carte bancaire',
            'edinar' => 'e-Dinar',
            'cod' => 'Paiement à la livraison',
            default => $method,
        };
    }
}
