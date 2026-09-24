<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus,
        public ?string $changedByName = null
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
        $actor = $this->changedByName ?? 'A user';

        return (new MailMessage)
            ->subject("Task Status Updated: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of task \"{$this->task->title}\" has been updated.")
            ->line("Status changed from \"{$this->oldStatus}\" to \"{$this->newStatus}\".")
            ->line("Updated by: {$actor}")
            ->line('Log in to the dashboard to view full task details.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $actor = $this->changedByName ?? 'A user';

        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => "Task \"{$this->task->title}\" status updated to {$this->newStatus} by {$actor}",
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by' => $this->changedByName,
            'type' => 'status_updated',
        ];
    }
}
