<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeptController extends AbstractController
{
    #[Route('/departements', name: 'app_dept')]
    public function index(DepartmentRepository $depts): Response
    {
        return $this->render('dept/index.html.twig', [
            'departements' => $depts->findAll(),
        ]);
    }
}
