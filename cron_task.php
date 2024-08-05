<?php
require 'config.php';
require 'email.php';
require 'botinfo.php';

// گرفتن اتصال به پایگاه داده از کلاس Singleton
$db = Database::getInstance();
$mysqli = $db->getConnection();

// گرفتن تاریخ فعلی
$current_date = date('Y-m-d');

// Query to get all users
$sql = "SELECT id, username, telegram_id FROM users";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// بارگذاری قالب ایمیل
$template = file_get_contents('email_template.html');

// Loop through each user
while ($user = $result->fetch_assoc()) {
    $user_id = $user['id'];
    $user_email = $user['username'];
    $telegram_id = $user['telegram_id'];

    // Query to get tasks for the current date for the current user
    $task_sql = "SELECT task FROM tasks WHERE user_id = ? AND task_date = ?";
    $task_stmt = $mysqli->prepare($task_sql);
    $task_stmt->bind_param("is", $user_id, $current_date);
    $task_stmt->execute();
    $task_result = $task_stmt->get_result();

    $tasks = [];
    while ($task_row = $task_result->fetch_assoc()) {
        $tasks[] = $task_row['task'];
    }

    // If there are tasks for today, send an email and a Telegram message
    if (!empty($tasks)) {
        $task_list = implode("<br>", array_map('htmlspecialchars', $tasks));

        // Prepare email content
        $subject = 'Reminder: Your Tasks for Today';
        $body = str_replace('{{task_list}}', $task_list, $template);

        // Send email
        sendEmail($user_email, $subject, $body);

        // Prepare Telegram message content
        $telegram_message = "Reminder: Your Tasks for Today\n" . implode("\n", $tasks);

        // Send Telegram message
        if (!empty($telegram_id)) {
            $post_fields = [
                'chat_id' => $telegram_id,
                'text' => $telegram_message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegram_api_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    $task_stmt->close();
}

$stmt->close();
$mysqli->close();
?>
