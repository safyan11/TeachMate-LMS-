<?php
$to = 'safimughal.com@gmail.com';
$subject = 'Test Email';
$message = 'This is a test email from XAMPP.';
$headers = 'From: safimughal.com@gmail.com' . "\r\n" .
           'Reply-To: safimughal.com@gmail.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully!';
} else {
    echo 'Failed to send email.';
    echo '<br>Error info: ' . error_get_last()['message'];
}
?>