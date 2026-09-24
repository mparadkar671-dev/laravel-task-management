<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public TaskComment $comment,
        public User $commenter
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Note on Task: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->commenter->name} added a note on task \"{$this->task->title}\":")
            ->line('"'.Str::limit($this->comment->comment, 150).'"')
            ->line('Log in to your workspace to view the full discussion.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => "{$this->commenter->name} commented on \"{$this->task->title}\"",
            'commenter_name' => $this->commenter->name,
            'comment_preview' => Str::limit($this->comment->comment, 80),
            'type' => 'task_comment',
        ];
    }
}
