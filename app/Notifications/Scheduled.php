<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Carbon\Carbon;

class Scheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $internship;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($internship)
    {
        $this->internship = $internship;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->subject('Internship visit slot scheduled')
                    ->line('Internship visit for student '.$this->internship->student->name.' have been scheduled to')
                    ->line(Carbon::parse($this->internship->scheduled)->format('l jS \\of F Y, h:i A'))
                    ->line('Student can submit a reschedule request in case the suggested time slot is not convenient.')
                    ->line('If you believe this email is not intended for you, no further action is required.')
                    ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
