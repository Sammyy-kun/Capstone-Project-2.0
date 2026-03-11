<?php
require __DIR__ . '/../Libraries/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../Libraries/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../Libraries/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        $mail->setFrom(MAIL_USER, 'Capstone System');
        $mail->addAddress($to);
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

