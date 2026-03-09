<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;
    protected $book;
    protected $reason;

    public function __construct(Review $review, Book $book, $reason = null)
    {
        $this->review = $review;
        $this->book = $book;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Update on Your Review - PageTurner')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for submitting a review for **"' . $this->book->title . '"**.')
            ->line('After review, we have decided not to publish your review at this time.');

        if ($this->reason) {
            $mailMessage->line('**Reason:** ' . $this->reason);
        }

        return $mailMessage
            ->line('We appreciate your contribution and encourage you to submit another review in the future.')
            ->line('If you have any questions, please contact our support team.')
            ->action('Browse Books', route('books.index'))
            ->salutation('PageTurner Team');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Review Not Approved',
            'message' => 'Your review for "' . $this->book->title . '" was not approved' . ($this->reason ? ': ' . $this->reason : ''),
            'type' => 'review',
            'review_id' => $this->review->id,
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'time' => now()->toDateTimeString(),
        ];
    }
}