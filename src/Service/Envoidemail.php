<?php
namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Envoidemail
{
    // constructeur
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
        )
    {
    }

    public function sendEmail($from, $to, $subject, $text)
    {
        $siteUrl = $this->urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }

    public function sendEmailCustom($from, $to, $subject, $params)
    {
        $siteUrl = $this->urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
       
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($params[0])
            ->htmlTemplate('relance/mail.html.twig')
            ->context([
                'titre' => $params[4],
                'produitListe' => $params[1],
                'datePret' => $params[2],
                'dateFinPrevue' => $params[3],
                'site' => $siteUrl,
            ]);
 
        $this->mailer->send($email);
    }
}
