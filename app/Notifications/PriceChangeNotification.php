<?php

namespace App\Notifications;

use App\Models\PriceChange;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceChangeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public PriceChange $change)
    {
        // GP - 19-08-2026 code comment - PriceChangeNotification constructor
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // GP - 19-08-2026 code comment - Determine delivery channels
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // GP - 19-08-2026 code comment - Build mail notification representation
        
        $url = url('/changes/' . $this->change->id);

        return (new MailMessage)
            ->subject("🚨 Competitor pricing changed")
            ->greeting("Competitor Pricing Shift Detected")
            ->line("We monitored a pricing change on the competitor's page:")
            ->line("**Competitor:** " . $this->change->competitor->name)
            ->line("**Plan:** " . ($this->change->plan?->name ?? '—'))
            ->line("**Previous:** " . ($this->change->old_value ?? '—'))
            ->line("**Current:** " . ($this->change->new_value ?? '—'))
            ->line("**Change:** " . $this->change->percentage_change . '%')
            ->line("**Detected:** " . $this->change->detected_at->format('F d, Y'))
            ->action('View Change', $url)
            ->line('Thank you for using PriceWatch!');
    }
}
