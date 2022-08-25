<?php

// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function sanitize_my_email($field)
{
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}
function validateData($data)
{
    $errors = [];
    if (empty($data['name'])) {
        $errors['name'] = 'Please enter a first name';
    }
    if (empty($data['message'])) {
        $errors['message'] = 'Please enter a message';
    }
    if (!sanitize_my_email($data['email'])) {
        $errors['email'] = 'Please enter a valid email address';
    }
    return $errors;

}
if (isset($_POST['mail'])) {
    $mailData = $_POST['mail'];
    foreach ($mailData as $key => $value) {
        $mailData[$key] = strip_tags($value);
    }
    if (empty(validateData($mailData))) {
        try {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
            $dotenv->safeLoad();
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], 'No reply');
            $mail->addReplyTo($mailData['email'], $mailData['name']);
            $mail->addAddress($_ENV['MAIL_TO_ADDRESS'], 'Sinpar Team');

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $mail->Subject = 'Demande de contact';
            $mail->Body = "<h1 style='text-align:center; margin:20px auto'>Demande de contact</h1><ul><li style='padding: 10px 5px'>Nom: {$mailData['name']}
        </li><li style='padding: 10px 5px'>Email: <a href='mailto:{$mailData['email']}'>{$mailData['email']}</a></li>
        <li style='padding: 10px 5px'>Message: <div style='padding: 20px 5px'>{$mailData['message']}</div>
        </li></ul><em style='text-align:center'>Cet email a été envoyé via le formulaire de contact de site web.</em>";
            $mail->send();
            echo json_encode("sent");
        } catch (Exception $e) {
            // output error message if fails
            echo json_encode("fail");
        }
    } else {
        $errors = validateData($mailData);
        echo json_encode([
            "validationError" => $errors,
        ]);
    }
}