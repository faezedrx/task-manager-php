<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $mysqli->prepare("SELECT username FROM users WHERE token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username);
        $stmt->fetch();

        $stmt->close();

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
