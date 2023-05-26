<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private $order,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $status = $this->order->status;

        return [
            'id' => $this->order->id,
            'status' => $status == 0 ? 'Rejected' : ($status == '1' ? 'Pending' : 'Approved'),
            'from_date' => $this->order->from_date,
            'to_date' => $this->order->to_date,
            'commercial_name' => $this->order->commercial_name,
            'scientific_name' => $this->order->scientific_name,
            'quantity' => $this->order->quantity,
            'offer_from_name' => $this->order->offer_from_name,
        ];
    }
}
