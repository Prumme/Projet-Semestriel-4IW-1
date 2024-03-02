<?php

namespace App\Service;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;

class EmailService
{
    private $apiInstance;

    public function __construct(string $apiKey)
    {
        // Configuration de l'API Sendinblue avec la clé API
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        $this->apiInstance = new TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
    }

    public function sendEmail(string $to, string $subject, string $content): bool
    {
        // Création de l'objet e-mail
        $email = new SendSmtpEmail();
        $email->setSender(new SendSmtpEmailSender(['email' => 'contact@apagnan.com', 'name' => 'Apagnan']));
        $email->setTo([new SendSmtpEmailTo(['email' => $to])]);
        $email->setSubject($subject);
        $email->setHtmlContent($content);

        try {
            // Envoi de l'e-mail via l'API Sendinblue
            $this->apiInstance->sendTransacEmail($email);
            return true;
        } catch (\Exception $e) {
            // Gestion des erreurs
            error_log('Exception lors de l\'envoi de l\'e-mail: ' . $e->getMessage());
            return false;
        }
    }

    public function sendEmailWithTemplate(string $to, int $templateId, array $templateVariables): bool
    {
        $to = "axel77g@hotmail.com";
        $email = new SendSmtpEmail();
        $email->setSender(new SendSmtpEmailSender(['email' => 'contact@apagnan.com', 'name' => 'Apagnan']));
        $email->setTo([new SendSmtpEmailTo(['email' => $to])]);
        $email->setTemplateId($templateId);
        $templateVariablesObject = (object)$templateVariables;
        $email->setParams($templateVariablesObject); // Paramètres pour remplacer les variables du template
        try {
            $this->apiInstance->sendTransacEmail($email);
            return true;
        } catch (\Exception $e) {
            error_log('Exception lors de l\'envoi de l\'e-mail avec template: ' . $e->getMessage());
            return false;
        }
    }
}
