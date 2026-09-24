<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
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
            ->error()
            ->subject("URGENT: Task Overdue - {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line('The following task assigned to you is past its due date:')
            ->line("Task: \"{$this->task->title}\"")
            ->line('Due Date: '.($this->task->due_date ? $this->task->due_date->format('Y-m-d') : 'None'))
            ->line('Priority: '.ucfirst($this->task->priority))
            ->line('Current Status: '.ucfirst($this->task->status))
            ->line('Please review and update this task as soon as possible.');
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
            'message' => "URGENT: Task \"{$this->task->title}\" is past due date!",
            'due_date' => $this->task->due_date?->format('Y-m-d'),
            'priority' => $this->task->priority,
            'status' => $this->task->status,
            'type' => 'task_overdue',
        ];
    }
}
