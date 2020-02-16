<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.11.2019
 * Time: 23:28
 */

namespace App\Services\EmailSendler;



use Monolog\Logger;

class EmailSendler
{
    private $mailer;
    public function __construct($config)
    {
        $transport = (new \Swift_SmtpTransport($config['host'], $config['port']))
            ->setUsername($config['login'])
            ->setPassword($config['password'])
            ->setEncryption('ssl');

        $this->mailer = new \Swift_Mailer($transport);
    }

    public function sendInvite($email, $token)
    {
        $message = (new \Swift_Message('Invite to registration on GTO service'))
            ->setFrom(['gto_service@gtoservice.ru' => 'GTO'])
            ->setTo([$email => ''])
            ->setBody('чтобы зарегистрироваться, пройдите по ссылке: <a href="http://gtoservice.ru/user/invite/'.$token.'">ссылка</a>', 'text/html');

        $this->mailer->send($message);
    }
}