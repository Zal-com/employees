<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/email')]
class MailerController extends AbstractController
{
    #[Route('/')]
    public static function sendEmail(MailerInterface $mailer, Employee $employee): Response
    {

        $email = (new Email())
            ->from('employees@test.com')
            ->to($employee->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Votre profil d\'employé a été créé, voici vos identifiants : ')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo print_r($e, TRUE);
        }
    }
}
