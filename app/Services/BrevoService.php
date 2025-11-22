<?php

namespace App\Services;

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;

class BrevoService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $this->apiInstance = new TransactionalEmailsApi(null, $config);
    }

    public function sendEmail($to, $subject, $htmlContent)
    {
        $email = new SendSmtpEmail([
            'sender' => ['name' => config('app.name'), 'email' => env('MAIL_FROM_ADDRESS')],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ]);

        return $this->apiInstance->sendTransacEmail($email);
    }
}
