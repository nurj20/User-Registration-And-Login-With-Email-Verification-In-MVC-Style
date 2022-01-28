<?php

require 'model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $verification_code = register($name, $email, $password);
    if ($verification_code === -1)
        echo json_encode(['status' => 'Something went wrong. Try again later!!']);
    else {
        sendMail($email, $verification_code);
        echo json_encode(['status' => 'Thanks for Registring with Us. Your account will be activated once you verify your email']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $status = login($name, $password);
    if ($status === 1)
        echo json_encode(['status' => "Welcome $name. You have successfully logged in."]);
    elseif ($status === 0)
        echo json_encode(['status' => "You have not yet verified your registration."]);
    else
        echo json_encode(['status' => 'Either user name or password is incoorect or You are not registered with Us']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    $code = $_GET['code'];
    $status = verify($code);
    if ($status === 1)
        header("Location: http://localhost:8080/showVerificationStatus.html?status=verified");
    else
        header("Location: http://localhost:8080/showVerificationStatus.html?status=verification_unsuccessful");
}
