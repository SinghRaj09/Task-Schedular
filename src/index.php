<?php
require_once 'functions.php';

// Handle adding new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-name'])) {
    $task_name = trim($_POST['task-name']);
    if (!empty($task_name)) {
        addTask($task_name);
    }
}

// Handle email subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);
    if (!empty($email)) {
        subscribeEmail($email);
    }
}

// Handle marking task complete/incomplete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle-task'])) {
    $task_id = $_POST['task-id'];
    $completed = $_POST['completed'] === 'true';
    markTaskAsCompleted($task_id, $completed);
}

// Handle deleting task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-task'])) {
    $task_id = $_POST['task-id'];
    deleteTask($task_id);
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .completed {
            text-decoration: line-through;
            color: gray;
        }
        .task-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>📋 Task Planner</h1>

    <!-- Add Task Form -->
    <form method="POST">
        <input type="text" name="task-name" placeholder="Enter new task" required>
        <button type="submit">Add Task</button>
    </form>

    <!-- Email Subscription Form -->
    <form method="POST" style="margin-top: 20px;">
        <input type="email" name="email" placeholder="Enter your email" required />
        <input type="hidden" name="subscribe" value="1">
        <button type="submit">Subscribe</button>
    </form>

    <!-- Task List -->
    <ul class="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item<?= $task['completed'] ? ' completed' : '' ?>">
                <!-- Toggle completion form -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task-id" value="<?= htmlspecialchars($task['id']) ?>">
                    <input type="hidden" name="toggle-task" value="1">
                    <input type="hidden" name="completed" value="<?= $task['completed'] ? 'false' : 'true' ?>">
                    <input type="checkbox" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
                </form>

                <?= htmlspecialchars($task['name']) ?>

                <!-- Delete task form -->
                <form method="POST" style="display:inline; margin-left:10px;">
                    <input type="hidden" name="task-id" value="<?= htmlspecialchars($task['id']) ?>">
                    <button type="submit" name="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
