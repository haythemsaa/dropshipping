<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product, public ?string $reason = null)
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
        $message = (new MailMessage)
            ->subject('Votre produit a été rejeté')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre produit \"{$this->product->name}\" n'a pas été approuvé.");

        if ($this->reason) {
            $message->line("**Raison du rejet :** {$this->reason}");
        }

        $message->line('Vous pouvez modifier votre produit et le soumettre à nouveau pour approbation.')
            ->action('Modifier le produit', route('supplier.products.edit', $this->product))
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
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
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'rejected_at' => now(),
            'reason' => $this->reason,
        ];
    }
}
