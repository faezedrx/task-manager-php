<?php
function getEmailTemplate($reset_link) {
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
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
        <div class='container'>
            <div class='header'>
                <h1>Dear User</h1>
            </div>
            <div class='content'>
                <p>We received a request to reset your password. If you did not request a password reset, please ignore this email.</p>
                <p>To reset your password, please click the link below:</p>
                <p><a href='$reset_link' class='button'>click here</a></p>
            </div>
            <div class='footer'>
                <p>If you did not request this email, please ignore it.</p>
                <p>Best Regards,<br>BAMBOO</p>
            </div>
        </div>
    </body>
    </html>";
}
?>
