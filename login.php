<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';
require 'email.php';
require 'email_template.php';

header('Content-Type: application/json');

$response = array();
$log_file = 'log.txt'; // فایل لاگ

file_put_contents($log_file, "Request received\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    file_put_contents($log_file, "POST request\n", FILE_APPEND);

    if (isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];
        file_put_contents($log_file, "Form type: $form_type\n", FILE_APPEND);

        $db = Database::getInstance();
        $mysqli = $db->getConnection();

        if ($form_type == 'login') {
            // کد ورود به سیستم
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            file_put_contents($log_file, "Login attempt with username: $username\n", FILE_APPEND);

            if (empty($username) || empty($password)) {
                $response['status'] = 'error';
                $response['message'] = 'Username and password are required!';
            } else {
                $stmt = $mysqli->prepare("SELECT nickname, password FROM users WHERE username = ? AND is_verified = 1");

                if ($stmt === false) {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to prepare statement for login: ' . $mysqli->error;
                    file_put_contents($log_file, "Prepare statement failed: " . $mysqli->error . "\n", FILE_APPEND);
                } else {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($nickname, $hashed_password);

                    if ($stmt->num_rows === 1) {
                        $stmt->fetch();
                        if (password_verify($password, $hashed_password)) {
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
            }
        } elseif ($form_type == 'forgot_password') {
            // کد فراموشی رمز عبور
            $email = trim($_POST['email']);

            file_put_contents($log_file, "Forgot password attempt with email: $email\n", FILE_APPEND);

            if (empty($email)) {
                $response['status'] = 'error';
                $response['message'] = 'Email is required!';
            } else {
                $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");

                if ($stmt === false) {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to prepare statement for checking email: ' . $mysqli->error;
                    file_put_contents($log_file, "Prepare statement failed: " . $mysqli->error . "\n", FILE_APPEND);
                } else {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows === 1) {
                        $reset_token = bin2hex(random_bytes(16));
                        $stmt->close();

                        $stmt = $mysqli->prepare("UPDATE users SET token = ?, token_created_at = NOW() WHERE username = ?");

                        if ($stmt === false) {
                            $response['status'] = 'error';
                            $response['message'] = 'Failed to prepare statement for updating token: ' . $mysqli->error;
                            file_put_contents($log_file, "Prepare statement failed: " . $mysqli->error . "\n", FILE_APPEND);
                        } else {
                            $stmt->bind_param("ss", $reset_token, $email);
                            $stmt->execute();
                            $stmt->close();

                            $reset_link = "https://bamboo-services.ir/reset_password.php?token=$reset_token";
                            $subject = "Password Reset Request";
                            $body = getEmailTemplate($reset_link);

                            if (sendEmail($email, $subject, $body)) {
                                $response['status'] = 'success';
                                $response['message'] = 'Password reset link has been sent to your email.';
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Failed to send password reset email.';
                            }
                        }
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Email not found!';
                    }
                }
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid form type!';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Form type is required!';
    }

    echo json_encode($response);
    file_put_contents($log_file, "Response: " . json_encode($response) . "\n", FILE_APPEND);
    exit();
} else {
    file_put_contents($log_file, "Invalid request method\n", FILE_APPEND);
}
?>
