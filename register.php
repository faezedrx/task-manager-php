<?php
require 'config.php';
require 'email.php';

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
            $db = Database::getInstance();
            $mysqli = $db->getConnection();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16)); // ایجاد یک توکن یکتا
            $stmt = $mysqli->prepare("INSERT INTO users (nickname, username, password, token, is_verified) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("ssss", $nickname, $username, $hashed_password, $token);

            if ($stmt->execute()) {
                // ارسال ایمیل تایید
                $email_body = file_get_contents('email_body.html');
                $email_body = str_replace('{{token}}', $token, $email_body);

                if (sendEmail($username, 'Email Verification', $email_body)) {
                    $response['status'] = 'success';
                    $response['message'] = 'Registration successful! Please verify your email.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Mail could not be sent. Check the error log for more details.';
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
