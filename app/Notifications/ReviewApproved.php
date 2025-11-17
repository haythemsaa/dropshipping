<?php

namespace App\Notifications;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApproved extends Notification implements ShouldQueue
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
            ->subject('Votre avis a été publié !')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Merci d\'avoir pris le temps de laisser un avis sur notre plateforme !')
            ->line('')
            ->line('Votre avis sur **' . $this->review->product->name . '** a été approuvé et est maintenant **visible publiquement**.')
            ->line('')
            ->line('**Votre note:** ' . $stars . ' (' . $this->review->rating . '/5)')
            ->line('**Votre avis:** ' . $this->review->comment)
            ->line('')
            ->action('Voir le produit', route('products.show', $this->review->product->slug) . '#reviews')
            ->line('Votre avis aide les autres clients à faire leur choix. Merci pour votre contribution !')
            ->line('')
            ->line('Vous pouvez modifier votre avis dans les 48 heures suivant sa publication si nécessaire.');
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
        ];
    }
}
