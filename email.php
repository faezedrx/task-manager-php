<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Initialize PHPMailer
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = '...';
$mail->SMTPAuth = true;
$mail->Username = '...';
$mail->Password = '...';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

function sendEmail($recipient, $subject, $body) {
    global $mail;

    try {
        $mail->setFrom('...', 'Task Manager');
        $mail->addAddress($recipient);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>
