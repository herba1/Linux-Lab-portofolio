<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Username = 'hrene2412@gmail.com';  // Replace with your email
    $mail->Password = 'urbx miau locv uiqb';    // Replace with your Gmail App Password

    // Recipients
    $mail->setFrom('hrene2412@gmail.com'); // Sender
    $mail->addAddress('herbart@mail.fresnostate.edu'); // Recipient


    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Jin Park saved me';
    $mail->Body = 'Whats good unc';

    // Send the email
    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

