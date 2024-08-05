# 📋 Task Manager with Email Reminders

Welcome to the Task Manager project! This application allows users to manage their tasks and receive email reminders for tasks scheduled for the day. It's built using PHP, MySQL, and PHPMailer.
# [use online](https://bamboo-services.ir/)

## 🚀 Features

- 📝 **Add, Edit, and Delete Tasks**: Manage your tasks effortlessly.
- 📅 **Email Reminders**: Receive email reminders for tasks scheduled for the current day.
- 🔒 **User Authentication**: Secure login to manage your tasks.
- 📧 **Immediate Email Notifications**: Send immediate email reminders for tasks on a selected date.

## 🛠️ Installation

1. **Clone the repository**:
    ```bash
    git clone https://github.com/faezedrx/task-manager-php.git
    cd task-manager-php
    ```

2. **Install Dependencies**:
    - Download and include PHPMailer:
      ```bash
      composer require phpmailer/phpmailer
      ```

3. **Configure Database**:
    - Create a database and import the `bamboos1_services_portf.sql` file provided in the repository.
    - Update the `config.php` file with your database credentials.

4. **Configure Email Settings**:
    - Update the `email.php` file with your SMTP server details.

## 🏃 Usage

- **Start the Application**:
    - Deploy the application on a local server or any web hosting service.
    - Access the application through your browser.

- **User Authentication**:
    - Register a new user or login with an existing account.

- **Manage Tasks**:
    - Add new tasks with a specific date.
    - Edit or delete existing tasks.
    - Receive daily email reminders for the tasks scheduled for today.

## 📧 Email Configuration

The email reminders are handled by PHPMailer. Ensure that your SMTP settings in `email.php` are correctly configured:

```php
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
        $this->mail->Host = 'your-smtp-server';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'your-email@example.com'; 
        $this->mail->Password = 'your-email-password'; 
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
        $mailInstance->setFrom('your-email@example.com', 'Task Management');
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
```
## 🤝 Contributing

Contributions are welcome! Please create a pull request or open an issue to discuss any changes.

## 🌟 Acknowledgments

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) for handling the email functionality.
- [Tailwind CSS](https://tailwindcss.com/) for styling the application.
- [SweetAlert2](https://sweetalert2.github.io/) for beautiful alerts.

## Contact 📬

If you have any questions or need further assistance, feel free to contact us at [my email : faezeh.darbeheshti@gmail.com ](mailto:faezeh.darbeheshti@gmail.com).
