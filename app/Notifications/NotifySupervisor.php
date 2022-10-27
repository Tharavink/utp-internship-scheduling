<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifySupervisor extends Notification implements ShouldQueue
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
                    ->subject('Supervisor for '.$this->internship->student->name)
                    ->line('First of all, thank you for accepting our student '.$this->internship->student->name.' as a trainee.')
                    ->line('With you being the supervisor for our student, we are confident that our student will be ready to face working life at the end of internship.')
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
