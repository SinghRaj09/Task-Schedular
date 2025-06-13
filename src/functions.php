<?php
// File: functions.php

function addTask($task_name) {
    $task_id = uniqid();
    $task = [
        'id' => $task_id,
        'name' => $task_name,
        'completed' => false
    ];
    $allTasks = getAllTasks();
    $allTasks[] = $task;
    file_put_contents('tasks.txt', json_encode($allTasks, JSON_PRETTY_PRINT));
    return $task_id;
}

function getAllTasks() {
    if (!file_exists('tasks.txt')) return [];
    $content = file_get_contents('tasks.txt');
    return $content ? json_decode($content, true) : [];
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }
    file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);
    file_put_contents('tasks.txt', json_encode(array_values($tasks), JSON_PRETTY_PRINT));
}

function generateVerificationCode() {
    return rand(100000, 999999);
}

function subscribeEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    $code = generateVerificationCode();
    $pending = file_exists('pending_subscriptions.txt') ? json_decode(file_get_contents('pending_subscriptions.txt'), true) : [];

    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];

    file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));

    // Simulate sending a verification email
    $verify_link = "http://localhost:8000/verify.php?email=" . urlencode($email) . "&code=" . $code;
    echo "<p>✅ Verification code sent to <strong>$email</strong></p>";
    echo "<p>🔗 <a href='$verify_link'>Click here to verify your email</a></p>";
}

function verifySubscription($email, $code) {
    $pending = file_exists('pending_subscriptions.txt') ? json_decode(file_get_contents('pending_subscriptions.txt'), true) : [];

    if (isset($pending[$email]) && $pending[$email]['code'] == $code) {
        unset($pending[$email]);
        file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));

        $subscribers = file_exists('subscribers.txt') ? json_decode(file_get_contents('subscribers.txt'), true) : [];
        if (!in_array($email, $subscribers)) {
            $subscribers[] = $email;
            file_put_contents('subscribers.txt', json_encode($subscribers)); // single-line
        }
        return true;
    }
    return false;
}

function unsubscribeEmail($email) {
    $subscribers = file_exists('subscribers.txt') ? json_decode(file_get_contents('subscribers.txt'), true) : [];
    $subscribers = array_filter($subscribers, fn($e) => $e !== $email);
    file_put_contents('subscribers.txt', json_encode(array_values($subscribers))); // single-line
}

function sendTaskReminders() {
    $subscribers = file_exists('subscribers.txt') ? json_decode(file_get_contents('subscribers.txt'), true) : [];
    $tasks = getAllTasks();
    $pending_tasks = array_filter($tasks, fn($task) => !$task['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    if (empty($pending_tasks)) return;

    $subject = "Task Reminder: You have pending tasks!";
    $body = "Hello,\n\nYou have the following pending tasks:\n";
    foreach ($pending_tasks as $task) {
        $body .= "- " . $task['name'] . "\n";
    }
    $body .= "\nTo unsubscribe, click: http://localhost:8000/unsubscribe.php?email=" . urlencode($email);

    // Simulate sending email
    echo "📧 Sending to $email:\n$body\n\n";
}
