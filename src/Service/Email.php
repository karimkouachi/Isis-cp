<?php

namespace App\Service;

use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class Email
{
  	protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }
  
    public function send($infosMail) {

        $transport = (new \Swift_SmtpTransport('mail.infomaniak.com', 587))
          ->setUsername('contact@isis-cp.fr')
          ->setPassword('Pabloneruda973')
          ;

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message())
            ->setSubject('Nouvelle reservation')
            ->setFrom($infosMail['email'])
            ->setTo('contact@isis-cp.fr')
            ->setBody(
                $this->templating->render(
                    // templates/emails/registration.html.twig
                    'reservation/email.html.twig', [
                        'infosMail' => $infosMail
                    ]),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $message;
    }
  
  	public function send_contact($infosMail) 
    {
        $transport = (new \Swift_SmtpTransport('mail.infomaniak.com', 587))
          ->setUsername('contact@isis-cp.fr')
          ->setPassword('Pabloneruda973')
          ;

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message())
            ->setSubject('Nouveau contact')
            ->setFrom($infosMail['Email'])
            ->setTo('contact@isis-cp.fr')
            ->setBody(
                $this->templating->render(
                    // templates/emails/registration.html.twig
                    'vtc/email.html.twig', [
                        'infosMail' => $infosMail
                    ]),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $message;
    }
}