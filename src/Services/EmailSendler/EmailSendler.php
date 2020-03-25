<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 23:28
 */

namespace App\Services\EmailSendler;



use Exception;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class EmailSendler
{
    private $mailer;
    public function __construct($config)
    {
        $transport = (new Swift_SmtpTransport($config['host'], $config['port']))
            ->setUsername($config['login'])
            ->setPassword($config['password'])
            ->setEncryption('ssl');

        $this->mailer = new Swift_Mailer($transport);
}

    public function sendInvite($email, $token)
    {
        $message = (new Swift_Message('Invite to registration on GTO service'))
            ->setFrom(['gto_service@gtoservice.ru' => 'GTO'])
            ->setTo([$email => ''])
            ->setBody('чтобы зарегистрироваться, пройдите по ссылке: <a href="http://gtoservice.ru/registration/confirm?token='.$token.'&email='.$email.'">ссылка</a>', 'text/html');

        $failedRecipients = [];
        $this->mailer->send($message, $failedRecipients);
        if (count($failedRecipients) != 0){
            throw new Exception();
        }
    }
}