<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Shipment $shipment)
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
        $order = $this->shipment->order;

        $message = (new MailMessage)
            ->subject("Votre commande #{$order->order_number} a été expédiée !")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Bonne nouvelle ! Votre commande a été expédiée.");

        if ($this->shipment->tracking_number) {
            $message->line("**Numéro de suivi :** {$this->shipment->tracking_number}");
        }

        if ($this->shipment->carrier) {
            $message->line("**Transporteur :** {$this->shipment->carrier}");
        }

        $message->line("**Date d'expédition :** " . $this->shipment->shipped_at->format('d/m/Y'))
            ->action('Suivre ma commande', route('orders.show', $order))
            ->line("Vous serez informé dès la livraison de votre colis.")
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
            'shipment_id' => $this->shipment->id,
            'order_id' => $this->shipment->order_id,
            'tracking_number' => $this->shipment->tracking_number,
        ];
    }
}
