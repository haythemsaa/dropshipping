<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product)
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
        return (new MailMessage)
            ->subject('Votre produit a été approuvé !')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Bonne nouvelle ! Votre produit \"{$this->product->name}\" a été approuvé.")
            ->line('Votre produit est maintenant visible sur la plateforme et peut être acheté par les clients.')
            ->line("**Prix :** {$this->product->price} DT")
            ->line("**Catégorie :** {$this->product->category->name}")
            ->action('Voir le produit', route('supplier.products.edit', $this->product))
            ->line('Merci de contribuer à notre marketplace !')
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
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'approved_at' => now(),
        ];
    }
}
