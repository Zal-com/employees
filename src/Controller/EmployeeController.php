<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\DeptEmp;
use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\DepartmentRepository;
use App\Repository\DeptEmpRepository;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function edit(Request $request, Employee $employee, EmployeeRepository $employeeRepository, DeptEmpRepository $deptEmpRepo): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, 'User tried to access page without having permissions');
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $file = $form->get('photo')->getData();
                $file->move('../public/images/employee', $file->getClientOriginalName());

                $employee->setPhoto('employee/' . $file->getClientOriginalName());

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

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            try {
                $employeeRepository->remove($employee, true);
            } catch(ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error','This employee has salaries!');

                return $this->redirectToRoute('app_employee_show', ['id' => $employee->getId()], 303);
            }
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create', name: 'app_employee_create', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        EmployeeRepository $employeeRepository,
                        Employee $employee, DepartmentRepository $deptRepo,
                        DeptEmpRepository $deptEmpRepo,
                        DeptEmp $deptEmp,
                        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "User tried to access page without having permissions");

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Trouver l'id du dernier employé
            $lastEmp = $employeeRepository->findBy([], ['id' => 'DESC'], 1, 0)[0]->getId();
            $id = (int) $lastEmp+1;

            $employee->setId($id);
            //generate random password and sets it
            $random = random_bytes(10);

            $employee->setPassword($passwordHasher->hashPassword($employee, $random));


            $dept = new DeptEmp();
            $dept->setEmployee($employee);
            $dept->setFromDate(new \DateTime('now'));
            $dept->setToDate(new \DateTime('31-12-9999'));
            $dept->setDepartment($deptRepo->find($request->get('employee')['departments'][0]));

            $employee->addDepartment($dept->getDepartment());
            $employeeRepository->save($employee, true);
            $deptEmpRepo->save($dept, true);
            //End password generation

            return $this->redirectToRoute('app_employee_index', ['pass' => $random], Response::HTTP_SEE_OTHER);
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
