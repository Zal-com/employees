<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);

    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(EmployeeRepository $employees, DepartmentRepository $departments): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Must be admin to access.");

        return $this->render('admin/index.html.twig', [
            'employees' => $employees->findAll(),
            'departments' => $departments->findAll()
        ]);
    }
}
