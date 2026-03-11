<?php
require "../config/database.php";
require "../models/user.php";
require "../vendor/PHPMailer/PHPMailer.php";
require "../vendor/PHPMailer/SMTP.php";

$db = (new Database())->connect();
$user = new User($db);

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = "client"; // FIXED

// VALIDATIONS
if (preg_match('/[0-9]/',$name)) exit("NAME_INVALID");
if (!filter_var($email,FILTER_VALIDATE_EMAIL)) exit("EMAIL_INVALID");
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',$password)) exit("WEAK_PASSWORD");

// CHECK EMAIL EXISTS
if ($user->emailExists($email)) exit("EMAIL_EXISTS");

// HASH + TOKEN
$pass = hash("sha256",$password);
$token = bin2hex(random_bytes(30));

if ($user->register($name,$email,$pass,$role,$token)) {

    // SEND VERIFICATION MAIL
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "orbienydan1@gmail.com"; // email ko
    $mail->Password = "nyuh sddc soyh tnkb";    // app password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("orbienydan1@gmail.com","Capstone Client Registration");
    $mail->addAddress($email);
    $mail->Subject = "Verify your Client Account";
    $mail->Body = "Click to verify: http://localhost/auth/verify.php?token=$token";
    $mail->send();

    exit("CLIENT_OK");
}

exit("CLIENT_FAIL");
?>
