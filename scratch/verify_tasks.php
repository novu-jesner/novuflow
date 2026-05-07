<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;
use App\Models\User;

$totalTasks = Task::count();
$nonEmployeeTasks = Task::whereHas('assignee', function($query) {
    $query->where('role', '!=', 'Employee');
})->get();

echo "Total Tasks: " . $totalTasks . PHP_EOL;
echo "Tasks assigned to non-employees: " . $nonEmployeeTasks->count() . PHP_EOL;

if ($nonEmployeeTasks->count() > 0) {
    echo "Issues found:" . PHP_EOL;
    foreach ($nonEmployeeTasks as $task) {
        $user = $task->assignee;
        echo "- Task ID {$task->id} assigned to {$user->name} (Role: {$user->role})" . PHP_EOL;
    }
} else {
    echo "All tasks are correctly assigned to Employees." . PHP_EOL;
}
