<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';


$fullname = $_POST["name"];
$email = $_POST["email"];
$sub = $_POST["subject"];
$message = $_POST["message"];

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "wahadsafyan410@gmail.com";
    $mail->Password = "yzkb pubp jhhd bpiq";  // Note: consider using env variable
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;                      // TCP port to connect to

    // Recipients
    $mail->setFrom('wahadsafyan410@gmail.com', 'Inquiry');
    $mail->addAddress('wahadsafyan410@gmail.com', 'Inquiry');  // Add a recipient

    // Content
    $mail->isHTML(true);                       // Set email format to HTML
    $mail->Subject = 'Here is the subject';

    // Email message content
    $message = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #e0e0e0;
            padding: 20px;
            background-color: #fafafa;
            border-radius: 8px;
        }
        .header {
            background-color: #1BA8DE;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Contact Information</h2>
        </div>

        <div class="content">
            <p><strong>Name:</strong> ' . htmlspecialchars($fullname) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
            <p><strong>Sub:</strong> ' . htmlspecialchars($sub) . '</p>
            <p><strong>Message:</strong> ' . htmlspecialchars($message) . '</p>
        </div>

        <div class="footer">
            <p>&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
';
    $mail->Body = $message;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();

    // Success message with Tailwind custom alert box
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#1BA8DE]">
    <div id="customAlert" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white text-black p-6 rounded-lg w-auto text-center">
            <p class="mb-4">Email sent successfully!</p>
            <button onclick="closeAlert()" class="bg-white text-[#1BA8DE] font-bold py-2 px-4 rounded hover:bg-gray-200">
                OK
            </button>
        </div>
    </div>

    <script>
        // Function to close the alert and redirect
        function closeAlert() {
            document.getElementById("customAlert").style.display = "none";
            document.location.href = "./index.php";
        }
    </script>
    </body>
    </html>
    ';
} catch (Exception $e) {
    // Error message with Tailwind custom alert box
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#1BA8DE]">
    <div id="errorAlert" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-red-500 text-white p-6 rounded-lg w-72 text-center">
            <p class="mb-4">Error: ' . $mail->ErrorInfo . '</p>
            <button onclick="closeErrorAlert()" class="bg-white text-red-500 font-bold py-2 px-4 rounded hover:bg-gray-200">
                OK
            </button>
        </div>
    </div>
 

    <script>
        // Function to close the error alert box
        function closeErrorAlert() {
            document.getElementById("errorAlert").style.display = "none";
             
            document.location.href = "./index.php";
        }
    </script>
    </body>
    </html>
    ';
}
