<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'Username and password are required!';
    } else {
        // آماده کردن پرس‌و‌جو برای دریافت رمز عبور و نیک‌نیم
        $stmt = $mysqli->prepare("SELECT nickname, password FROM users WHERE username = ? AND is_verified = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nickname, $hashed_password);

        if ($stmt->num_rows === 1) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // ذخیره‌سازی اطلاعات کاربر در سشن
                $_SESSION['username'] = $username;
                $_SESSION['nickname'] = $nickname;
                $response['status'] = 'success';
                $response['message'] = 'Login successful!';
                $response['redirect'] = 'home.php';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid username or password!';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid username or password!';
        }

        $stmt->close();
    }

    echo json_encode($response);
}
?>
