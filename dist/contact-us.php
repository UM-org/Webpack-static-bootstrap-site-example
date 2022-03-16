<?php

// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require_once '../vendor/autoload.php';

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;

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
        $errors['email'] = 'Please enter a valid email adress';
    }
    return $errors;

}
function sesMail($body)
{
    $dotenv = \Dotenv\Dotenv::createImmutable("C:/Program Files/xampp/htdocs/sinpar");
    $dotenv->safeLoad();
    $credentials = new Credentials($_ENV['AWS_ACCESS_KEY_ID'], $_ENV['AWS_SECRET_ACCESS_KEY']);
    // Create an SesClient. Change the value of the region parameter if you're
    // using an AWS Region other than US West (Oregon). Change the value of the
    // profile parameter if you want to use a profile in your credentials file
    // other than the default.
    $body['Destination'] = [
        'ToAddresses' => [$_ENV['CONTACT_MAIL_ADDRESS'] ?? "hassen@ulysse.media"],
    ];
    $body['Source'] = $_ENV['MAIL_FROM_ADDRESS'] ?? "noreply@sinpar.tn";
    try {
        $SesClient = new SesClient([
            'version' => '2010-12-01',
            'region' => 'eu-west-1',
            'credentials' => $credentials,
        ]);
        $result = $SesClient->sendEmail($body);
        return $result['MessageId'];
    } catch (\Throwable $th) {
        return false;
    }
}
if (isset($_POST['mail'])) {
    $mailData = $_POST['mail'];
    foreach ($mailData as $key => $value) {
        $mailData[$key] = strip_tags($value);
    }
    if (empty(validateData($mailData))) {
        $subject = 'Contact';
        $html_body = "<p>Cet email a été envoyé par le site web via le formulaire de contact.</p><ul><li>Nom: {$mailData['name']}
        </li><li>Email: <a href='mailto:{$mailData['email']}'>{$mailData['email']}</a></li>
        <li>Message: <div>{$mailData['message']}</div>
        </li></ul>";
        $body = [
            'ReplyToAddresses' => [$mailData['email']],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => "UTF-8",
                        'Data' => $html_body,
                    ],
                ],
                'Subject' => [
                    'Charset' => "UTF-8",
                    'Data' => $subject,
                ],
            ],
        ];
        $result = sesMail($body);
        if($result){
            echo json_encode("sent");
        }else{
            echo json_encode("fail");
        }
    } else {
        $errors = validateData($mailData);
        echo json_encode([
            "validationError" => $errors,
        ]);
    }
}
