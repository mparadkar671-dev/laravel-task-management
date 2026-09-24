<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Console\Command;

class SendOverdueTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:remind-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all overdue tasks and send reminders to assigned users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $overdueTasks = Task::with('assignedTo')
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($overdueTasks as $task) {
            if ($task->assignedTo) {
                $task->assignedTo->notify(new TaskOverdueNotification($task));
                $count++;
            }
        }

        $this->info("Successfully dispatched {$count} overdue reminder notifications.");

        return self::SUCCESS;
    }
}
