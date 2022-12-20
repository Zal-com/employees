<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration', methods: ['POST'])]
    public function index(UserPasswordHasherInterface $pwdHasher, Request $request): Response
    {
        dd($request);
        $user = new User();

        $plaintextPassword = $request->getPassword();

        $hashedPassword = $pwdHasher->hashPassword($user, $plaintextPassword);

        $user->setPassword($hashedPassword);
        $user->setEmail();
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }
}
