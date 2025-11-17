<?php

namespace App\Notifications;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProductReview extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProductReview $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductReview $review)
    {
        $this->review = $review;
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
        $stars = str_repeat('⭐', $this->review->rating);

        return (new MailMessage)
            ->subject('Nouvel avis sur votre produit - ' . $this->review->product->name)
            ->greeting('Bonjour ' . $notifiable->business_name . ',')
            ->line('Un client a laissé un nouvel avis sur votre produit.')
            ->line('')
            ->line('**Produit:** ' . $this->review->product->name)
            ->line('**Note:** ' . $stars . ' (' . $this->review->rating . '/5)')
            ->line('**Client:** ' . $this->review->user->name)
            ->line('')
            ->line('**Titre:** ' . ($this->review->title ?? 'Sans titre'))
            ->line('**Commentaire:** ' . $this->review->comment)
            ->line('')
            ->line('Cet avis est actuellement **en attente de modération** par notre équipe. Il sera publié après vérification.')
            ->action('Voir le produit', route('products.show', $this->review->product->slug))
            ->line('Les avis clients sont importants pour améliorer votre offre et augmenter vos ventes.')
            ->line('Merci de votre confiance !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'product_name' => $this->review->product->name,
            'rating' => $this->review->rating,
            'customer_name' => $this->review->user->name,
        ];
    }
}
