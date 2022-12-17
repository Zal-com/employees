<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

class EmployeeController extends AbstractController
{
    #[Route('/employees', name: 'app_employee_index')]
    public function index(EmployeeRepository $employees): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employees->findAll(),
        ]);
    }

    #[Route('/employees/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response{
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }
}
