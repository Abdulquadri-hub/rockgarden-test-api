<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast','mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('New Announcement: ' . $this->announcement->title)
        ->view('emails.announcement', [
            'type' => 'announcement',
            'announcement' => $this->announcement
        ]);
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'priority' => $this->announcement->priority,
            'author' => $this->announcement->author->name
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'priority' => $this->announcement->priority,
            'author' => $this->announcement->author->name
        ]);
    }
}
