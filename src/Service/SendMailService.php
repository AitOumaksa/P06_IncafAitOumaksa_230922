<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport\TransportInterface;


class SendMailService
{
    private $mailer;

    public function __construct(TransportInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {  
        //On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $SentMessage = $this->mailer->send($email);
        // Voir Debug envoi mail
       // dd($SentMessage)->getDebug();
        
    }
}