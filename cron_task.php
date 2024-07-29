<?php
require 'config.php';
require 'email.php';

// Get current date
$current_date = date('Y-m-d');

// Query to get all users
$sql = "SELECT id, username FROM users";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Loop through each user
while ($user = $result->fetch_assoc()) {
    $user_id = $user['id'];
    $user_email = $user['username'];

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

    // If there are tasks for today, send an email
    if (!empty($tasks)) {
        $task_list = implode("<br>", array_map('htmlspecialchars', $tasks));

        // Prepare email content
        $subject = 'Reminder: Your Tasks for Today';
        $body = '
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        line-height: 1.6;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 10px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        text-align: center;
                        padding: 10px 0;
                    }
                    .header h1 {
                        color: #5ab45c;
                    }
                    .content {
                        padding: 20px;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 20px;
                        color: #777;
                    }
                    .task-card {
                        background-color: #f9f9f9;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        padding: 15px;
                        margin-bottom: 10px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .task-card h3 {
                        margin: 0 0 10px;
                        color: #333;
                    }
                    .task-card p {
                        margin: 0;
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Task Reminder</h1>
                    </div>
                    <div class="content">
                        <div class="task-card">
                            <h3>Your Tasks for Today</h3>
                            <p>' . $task_list . '</p>
                        </div>
                    </div>
                    <div class="footer">
                        <p>Best regards,<br>Your Task Management Team</p>
                    </div>
                </div>
            </body>
        </html>';

        // Send email
        sendEmail($user_email, $subject, $body);
    }
    
    $task_stmt->close();
}

$stmt->close();
$mysqli->close();
?>
