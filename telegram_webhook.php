<?php
// فایل کانفیگ که اتصال به دیتابیس را برقرار می‌کند
require 'config.php';

// خواندن داده‌های دریافتی از تلگرام
$update = json_decode(file_get_contents("php://input"), true);

if ($update) {
    $message = $update['message'];
    $telegram_id = $message['from']['id'];
    $username = $message['from']['username'] ?? '';

    if (isset($message['text']) && $message['text'] == '/start') {
        // ذخیره یا به‌روزرسانی Telegram ID در دیتابیس
        $stmt = $mysqli->prepare("INSERT INTO users (username, telegram_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE telegram_id = ?");
        $stmt->bind_param('sss', $username, $telegram_id, $telegram_id);
        $stmt->execute();
        $stmt->close();

        // ارسال پیام خوش‌آمدگویی به کاربر
        $response = [
            'chat_id' => $telegram_id,
            'text' => "Welcome! Your Telegram ID has been saved."
        ];
        file_get_contents("https://api.telegram.org/bot" . "7432963901:AAGnG0HIXRCDGLQBhGb5Ca0lhSz59VfnIwA" . "/sendMessage?" . http_build_query($response));
    }
}
$mysqli->close();
?>
