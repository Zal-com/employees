<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/email')]
class MailerController extends AbstractController
{
    #[Route('/')]
    public function sendEmail(MailerInterface $mailer, Employee $employee): Response
    {
        $email = (new Email())
            ->from('employees@test.com')
            ->to('st.guillaume@outlook.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Votre profil d\'employé a été créé, voici vos identifiants : ')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
    }
}
