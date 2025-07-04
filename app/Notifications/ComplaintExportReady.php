<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintExportReady extends Notification implements ShouldQueue
{
    use Queueable;

    protected $filePath;
    protected $recipientName;

    // This constructor now correctly receives 'exports/your-file.xlsx'
    public function __construct(string $filePath, string $recipientName)
    {
        $this->filePath = $filePath;
        $this->recipientName = $recipientName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // This asset() helper now works perfectly because $this->filePath is correct.
        $downloadUrl = asset('storage/' . $this->filePath);

        return (new MailMessage)
            ->subject('Your Complaint Report is Ready for Download')
            ->greeting('Hello ' . $this->recipientName . ',')
            ->line('The complaint report you requested has been generated successfully.')
            ->action('Download Report', $downloadUrl)
            ->line('This link will be available for a limited time.')
            ->line('Thank you for using our application!');
    }
}