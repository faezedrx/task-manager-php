<?php
require 'config.php';  // فایل تنظیمات و اتصال به پایگاه داده را وارد کنید
require 'email.php';  // فایل مربوط به تنظیمات ارسال ایمیل
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $response['status'] = 'error';
        $response['message'] = 'Email is required!';
    } else {
        // چک کردن وجود ایمیل در پایگاه داده
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                // ایجاد توکن بازنشانی رمز عبور
                $reset_token = bin2hex(random_bytes(16));
                $stmt->close();

                $stmt = $mysqli->prepare("UPDATE users SET token = ?, token_created_at = NOW() WHERE username = ?");
                if ($stmt) {
                    $stmt->bind_param("ss", $reset_token, $email);
                    $stmt->execute();
                    $stmt->close();

                    // ارسال ایمیل بازنشانی رمز عبور
                    $reset_link = "https://bamboo-services.ir/reset_password.php?token=$reset_token";
                    $subject = "Password Reset Request";
                    $body = "
                        <p>Dear User,</p>
                        <p>We received a request to reset your password. If you did not request a password reset, please ignore this email.</p>
                        <p>To reset your password, please click the link below:</p>
                        <p><a href='$reset_link'>click here</a></p>
                        <p>Best Regards,<br>BAMBOO</p>
                    ";

                    if (sendEmail($email, $subject, $body)) {
                        $response['status'] = 'success';
                        $response['message'] = 'Password reset link has been sent to your email.';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to send password reset email.';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to prepare statement for updating token.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Email not found!';
            }

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to prepare statement for checking email.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method!';
}

echo json_encode($response);
?>
