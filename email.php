<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php';

class Mail {
    private static $instance = null;
    private $mail;

    private function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    private function configureSMTP() {
        // تنظیمات سرور SMTP
        $this->mail->isSMTP();
        $this->mail->Host = 'bamboo-services.ir';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = ''; 
        $this->mail->Password = ''; 
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Mail();
        }
        return self::$instance;
    }

    public function getMailer() {
        return $this->mail;
    }
}

function sendEmail($to, $subject, $body) {
    $mailInstance = Mail::getInstance()->getMailer();

    try {
        // تنظیمات گیرنده
        $mailInstance->setFrom('', 'Task Management');
        $mailInstance->addAddress($to);

        // تنظیمات محتوا
        $mailInstance->isHTML(true);
        $mailInstance->Subject = $subject;
        $mailInstance->Body = $body;

        // ارسال ایمیل
        $mailInstance->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mailInstance->ErrorInfo}");
        return false;
    }
}
?>
