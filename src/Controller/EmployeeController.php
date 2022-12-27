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
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/employees')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee_index')]
    public function index(EmployeeRepository $employees): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employees->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Employee $employee): Response{
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, 'User tried to access page without having permissions');
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $employeeRepository->save($employee, true);

                $this->addFlash('success','The employee has been successfully updated.');

                return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error','The employee has not been updated.');
            }
        }

        return $this->renderForm('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'] )]
    public function delete(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            try {
                $employeeRepository->remove($employee, true);
            } catch(ForeignKeyConstraintViolationException $e) {
                //dump($e);die;
                $this->addFlash('error','This employee has salaries!');

                return $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], 303);
            }
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create', name: 'app_employee_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "User tried to access page without having permissions");
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->save($employee, true);

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employee/create.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/profile', name: 'app_employee_profile', methods: ['GET', 'POST'])]
    public function profile(EmployeeRepository $employees): Response
    {
     $this->denyAccessUnlessGranted("ROLE_USER");

     return $this->render('employee/profile.html.twig', [
         'employee' => $this->getUser()
     ]);
    }
}
