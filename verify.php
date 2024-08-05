<?php
require 'config.php'; 

// چک کردن اینکه آیا توکن در URL موجود است
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    // دریافت اتصال به پایگاه داده
    $db = Database::getInstance();
    $mysqli = $db->getConnection();

    // چک کردن توکن در پایگاه داده
    $stmt = $mysqli->prepare("SELECT username FROM users WHERE token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();

        // به‌روزرسانی وضعیت تایید
        $stmt = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        if ($stmt->execute()) {
            header('Location: index.php?verified=1');
            exit();
        } else {
            echo "Failed to verify your email.";
        }
        $stmt->close();
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>
