<?php

namespace App\Notifications;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Review $review;
    protected Book $book;
    protected User $reviewer;

    public function __construct(Review $review, Book $book, User $reviewer)
    {
        $this->review = $review;
        $this->book = $book;
        $this->reviewer = $reviewer;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Review Submitted - PageTurner')
            ->greeting('Hello Admin!')
            ->line('A new review has been submitted for **' . $this->book->title . '**.')
            ->line('**Review Details:**')
            ->line('- Reviewer: ' . $this->reviewer->name)
            ->line('- Rating: ' . $this->review->rating . '/5')
            ->line('- Comment: ' . ($this->review->comment ?? 'No comment'))
            ->action('View Book', route('books.show', $this->book))
            ->salutation('PageTurner Store');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Review: ' . $this->book->title,
            'message' => $this->reviewer->name . ' submitted a ' . $this->review->rating . '-star review',
            'type' => 'review',
            'review_id' => $this->review->id,
            'book_id' => $this->book->id,
            'action_url' => route('books.show', $this->book),
            'time' => now()->toDateTimeString(),
        ];
    }
}
