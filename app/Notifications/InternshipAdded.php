<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InternshipAdded extends Notification implements ShouldQueue
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
                    ->subject('Assigned as supervisor for internship')
                    ->line('You have been assigned as a supervisor for '.$this->internship->student->name.'.')
                    ->line('You may view the details of the internship by clicking the button below.')
                    ->action('View Internship', route('internship.detail', $this->internship->id))
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
