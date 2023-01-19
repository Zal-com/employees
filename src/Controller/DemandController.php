<?php

namespace App\Controller;

use App\Entity\Demand;
use App\Entity\DeptEmp;
use App\Entity\Employee;
use App\Entity\Salary;
use App\Form\DemandType;
use App\Repository\DemandRepository;
use App\Repository\DepartmentRepository;
use App\Repository\DeptEmpRepository;
use App\Repository\EmployeeRepository;
use App\Repository\SalaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/demand')]
class DemandController extends AbstractController
{
    #[Route('/', name: 'app_demand_index', methods: ['GET'])]
    public function index(DemandRepository $demandRepository): Response
    {
        return $this->render('demand/index.html.twig', [
            'demands' => $demandRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_demand_show', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function show(Request $request, DemandRepository $repo) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $employee = $request->get('id');
        $demandes = $repo->findBy(['employee' => $employee, 'status' => null]);

        return $this->render('demand/show.html.twig', [
            'demands' => $demandes
        ]);
    }

    #[Route('/new', name: 'app_demand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DemandRepository $demandRepository): Response
    {
        $demand = new Demand();
        $form = $this->createForm(DemandType::class, $demand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandRepository->save($demand, true);

            return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demand/new.html.twig', [
            'demand' => $demand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update/{status}', name: 'app_demand_update', requirements: ['id' => Requirement::DIGITS, 'status' =>'^[01]*$'])]
    public function update(Request $request, DemandRepository $demandRepo, SalaryRepository $salaryRepo, EmployeeRepository $empRepo, DepartmentRepository $deptRepo, DeptEmpRepository $deptEmpRepo) : Response{
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $status = $request->get('status');
        $id_demande = $request->get('id');
        $demand = $demandRepo->find($id_demande);

        if($status == 1){
            if($demand->getType() == 'raise'){
                $salary = (new Salary())
                    ->setEmployee($demand->getEmployee())
                    ->setSalary($demand->getAbout())
                    ->setFromDate(new \DateTime('now'))
                    ->setToDate((new \DateTime('31-12-9999')));

                $lastSalary = $demand->getEmployee()->getSalaries()->last();
                $lastSalary->setToDate(new \DateTime('now'));
                $salaryRepo->save($lastSalary, true);
                $salaryRepo->save($salary, true);
                $empRepo->save($demand->getEmployee()->addSalary($salary), true);

            }
            elseif ($demand->getType() == 'reaffectation'){
                $dept = $demand->getAbout();
                $employee = $demand->getEmployee();

                $newDeptEmp = (new DeptEmp())
                    ->setEmployee($employee)
                    ->setDepartment($deptRepo->find($dept))
                    ->setFromDate(new \DateTime('now'))
                    ->setToDate(new \DateTime('31-12-9999'));

                $deptEmpRepo->save($employee->getDeptEmp()->last()->setToDate(new \DateTime('now')), true);
                $deptEmpRepo->save($newDeptEmp, true);
            }
        }

        $demand->setStatus($status);

        $demandRepo->save($demand, true);

        return $this->redirectToRoute('app_demand_show', ['id' => $demand->getEmployee()->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_demand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demand $demand, DemandRepository $demandRepository): Response
    {
        $form = $this->createForm(DemandType::class, $demand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandRepository->save($demand, true);

            return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demand/edit.html.twig', [
            'demand' => $demand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demand_delete', methods: ['POST'])]
    public function delete(Request $request, Demand $demand, DemandRepository $demandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demand->getId(), $request->request->get('_token'))) {
            $demandRepository->remove($demand, true);
        }

        return $this->redirectToRoute('app_demand_index', [], Response::HTTP_SEE_OTHER);
    }
}