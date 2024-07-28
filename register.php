<?php
require 'config.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($nickname) || empty($username) || empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'Nickname, email, and password are required!';
    } else if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email format!';
    } else {
        // Validate password requirements
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must include at least one uppercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must include at least one number.';
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must include at least one special character.';
        }

        if (!empty($errors)) {
            $response['status'] = 'error';
            $response['message'] = implode(' ', $errors);
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16)); // ایجاد یک توکن یکتا
            $stmt = $mysqli->prepare("INSERT INTO users (nickname, username, password, token, is_verified) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("ssss", $nickname, $username, $hashed_password, $token);

            if ($stmt->execute()) {
                // ارسال ایمیل تایید
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = '...'; // تنظیمات SMTP سرور ایمیل شما
                $mail->SMTPAuth = true;
                $mail->Username = '...'; // ایمیل شما
                $mail->Password = '...'; // رمز ایمیل شما
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('tasks@bamboo-services.ir', 'BAMBOO');
                $mail->addAddress($username);

                $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body    = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        text-align: center;
                        padding: 20px;
                    }
                    .button {
                        display: inline-block;
                        padding: 10px 20px;
                        font-size: 16px;
                        color: #fff;
                        background-color: #5ab45c;
                        text-decoration: none;
                        border-radius: 5px;
                        transition: background-color 0.3s ease;
                    }
                    .button:hover {
                        background-color: #489b4a;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 20px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Verify Your Email</h1>
                    </div>
                    <div class="content">
                        <p>Thank you for registering with us!</p>
                        <p>Please click the button below to verify your email address.</p>
                        <a href="https://bamboo-services.ir/verify.php?token=' . $token . '" class="button">Verify Email</a>
                    </div>
                    <div class="footer">
                        <p>If you did not request this email, please ignore it.</p>
                    </div>
                </div>
            </body>
            </html>';

                if($mail->send()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Registration successful! Please verify your email.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Mail could not be sent.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error: ' . $stmt->error;
            }

            $stmt->close();
        }
    }

    echo json_encode($response);
}
?>
