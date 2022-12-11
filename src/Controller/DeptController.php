<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeptController extends AbstractController
{
    #[Route('/dept', name: 'app_dept')]
    public function index(): Response
    {
        return $this->render('dept/index.html.twig', [
            'controller_name' => 'DeptController',
        ]);
    }
}
