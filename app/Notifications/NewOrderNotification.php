<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
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
        // Count items for this supplier
        $supplierItems = $this->order->items()->whereHas('product', function($query) use ($notifiable) {
            $query->where('supplier_id', $notifiable->id);
        })->get();

        $itemCount = $supplierItems->count();
        $totalAmount = $supplierItems->sum('subtotal');

        return (new MailMessage)
            ->subject("Nouvelle commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Vous avez reçu une nouvelle commande !")
            ->line("**Détails de la commande :**")
            ->line("Numéro : **#{$this->order->order_number}**")
            ->line("Articles concernés : **{$itemCount}**")
            ->line("Montant total : **" . number_format($totalAmount, 2) . " DT**")
            ->action('Voir la commande', route('supplier.orders.show', $this->order))
            ->line("Veuillez préparer les articles pour expédition dès que possible.")
            ->line("Merci de votre collaboration !")
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
        ];
    }
}
