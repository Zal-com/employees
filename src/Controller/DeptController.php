<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/departements')]
class DeptController extends AbstractController
{
    #[Route('/', name: 'app_dept_index')]
    public function index(DepartmentRepository $depts, EmployeeRepository $employees): Response
    {
        return $this->render('dept/index.html.twig', [
            'departements' => $depts->findAll(),
        ]);
    }
    #[Route('/show/{id}', name:'app_dept_show', methods: ['GET'])]
    public function show(Department $department):Response{
        return $this->render('dept/show.html.twig',[
            'departement' => $department,
        ]);
    }
}
