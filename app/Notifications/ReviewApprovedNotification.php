<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;
    protected $book;

    public function __construct(Review $review, Book $book)
    {
        $this->review = $review;
        $this->book = $book;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Review has been Approved - PageTurner')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your review for **"' . $this->book->title . '"** has been approved.')
            ->line('Your review: "' . $this->review->comment . '"')
            ->line('Rating: ' . $this->review->rating . '/5')
            ->line('Thank you for sharing your thoughts with the PageTurner community!')
            ->action('View Book', route('books.show', $this->book->id))
            ->salutation('Keep Reading! 📚');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Review Approved',
            'message' => 'Your review for "' . $this->book->title . '" has been approved',
            'type' => 'review',
            'review_id' => $this->review->id,
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'time' => now()->toDateTimeString(),
        ];
    }
}