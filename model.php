<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
require 'vendor/autoload.php';
session_start();
$conn = new mysqli('localhost:3306', 'root', 'root', 'email_verification_db');
if ($conn->connect_errno) {
    echo json_encode(['status' => "$conn->connect_error"]);
    exit();
}

function register($name, $email, $password)
{
    global $conn;
    $verification_code = generate_code();
    $stmt = $conn->prepare("insert into users (name, email, password, verification_code) values(?, ?, ?, ?);");
    $stmt->bind_param('ssss', $name, $email, $password, $verification_code);
    $stmt->execute();
    if ($stmt->affected_rows > 0)
        $status = $verification_code;
    //Registration to db failed 
    else
        $status = -1;
    $stmt->close();
    return $status;
}

function verify($verification_code)
{
    global $conn;
    $stmt = $conn->prepare("update users set verification_status=1 where verification_code=?;");
    $stmt->bind_param('s', $verification_code);
    $stmt->execute();
    //Registration  verified successfully
    if ($stmt->affected_rows === 1)
        $status = 1;
    //Registration failed to be verified 
    else
        $status = 0;
    $stmt->close();
    return $status;
}

function login($name, $password)
{
    global $conn;
    $stmt = $conn->prepare("select verification_status from users where name =? and password=?;");
    $stmt->bind_param('ss', $name, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    // Record matching username and passowrd found 
    if ($result->num_rows === 1) {
        $status = $result->fetch_assoc();
        // Email has been verified
        if ($status['verification_status'] === 1) {
            $_SESSION['logged_user'] = $name;
            return 1;
        }
        // Email has not been verified
        else
            return 0;
    }
    // Record matching username and passowrd not found 
    else
        return -1;
}

function sendMail($email, $verification_code)
{
    $link = "<a href='http://localhost:8080/controller.php?code=$verification_code'>here</a>";
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'YOUR_EMAIL_ADDRESS_SHOULD_GO_HERE';                     //SMTP username
        $mail->Password   = 'YOUR_PASSWORD_SHOULD_GO_HERE';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('thedigitalnj@gmail.com', 'CodingShodingWithNJ');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Here is the subject';
        $mail->Body    = "Thanks for Registering with us. To activate your account click $link";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        echo json_encode(['status' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
}
function generate_code()
{ return bin2hex(openssl_random_pseudo_bytes(15));}