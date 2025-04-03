<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'config/email_config.php';


function sendResetEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Password Reset - Banana Catcher";
        
        // Create reset link
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/BananaCatcher/reset-password.php?token=" . $token;
        
        // Email content
        $message = "
        <html>
        <head>
            <title>Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
                .button { 
                    display: inline-block; 
                    padding: 10px 20px; 
                    background: #ffa500; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    margin: 20px 0;
                }
                .footer { 
                    text-align: center; 
                    margin-top: 30px; 
                    font-size: 12px; 
                    color: #666; 
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Banana Catcher</h1>
                    <h2>Password Reset Request</h2>
                </div>
                
                <div class='content'>
                    <p>Hello,</p>
                    
                    <p>We received a request to reset your password for your Banana Catcher account. If you didn't make this request, you can safely ignore this email.</p>
                    
                    <p>To reset your password, click the button below:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$reset_link}' class='button'>Reset Password</a>
                    </div>
                    
                    <p>Or copy and paste this link into your browser:</p>
                    <p>{$reset_link}</p>
                    
                    <p>This link will expire in 1 hour for security reasons.</p>
                    
                    <p>If you're having trouble clicking the button, copy and paste the URL above into your web browser.</p>
                </div>
                
                <div class='footer'>
                    <p>This is an automated message, please do not reply to this email.</p>
                    <p>If you didn't request this password reset, please ignore this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $message;
        $mail->AltBody = "To reset your password, click this link: {$reset_link}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?> 