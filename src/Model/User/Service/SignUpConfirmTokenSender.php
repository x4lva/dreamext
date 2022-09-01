<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Twig\Environment;

class SignUpConfirmTokenSender
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(string $email, string $token): void
    {
        $message = (new \Swift_Message('Sig Up Confirmation'))
            ->setTo($email)
            ->setBody($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]), 'text/html');

        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}