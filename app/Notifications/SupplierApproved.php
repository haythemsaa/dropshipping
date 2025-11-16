<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
            ->subject('Votre compte fournisseur a été approuvé !')
            ->greeting("Félicitations {$notifiable->name} !")
            ->line('Votre compte fournisseur a été approuvé par notre équipe.')
            ->line('Vous pouvez maintenant accéder à votre tableau de bord et commencer à ajouter vos produits.')
            ->line('**Prochaines étapes :**')
            ->line('1. Complétez votre profil fournisseur')
            ->line('2. Ajoutez vos premiers produits')
            ->line('3. Configurez vos options de livraison')
            ->action('Accéder au tableau de bord', route('supplier.dashboard'))
            ->line('Bienvenue dans la communauté Dropshipping Tunisia !')
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
            'user_id' => $notifiable->id,
            'approved_at' => now(),
        ];
    }
}
