<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Employee;
use App\Form\DeptType;
use App\Repository\DepartmentRepository;
use App\Repository\DeptManagerRepository;
use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/departements')]
class DeptController extends AbstractController
{
    #[Route('/', name: 'app_dept_index')]
    public function index(DepartmentRepository $depts): Response
    {
        return $this->render('dept/index.html.twig', [
            'departements' => $depts->findAll(),
            'employee' => $this->getUser()
        ]);
    }
    #[Route('/{id}', name:'app_dept_show', methods: ['GET'], requirements: ['id'=>'^d[0-9]+'])]
    public function show(Department $department):Response{
        //dd($department->getActualManager());  //TODO Heavy Model, Light Controller

        $managers = $department->getManagers();
        $actualManager = null;

        foreach($managers as $manager) {
            $stories = $manager->getManagingStories();

            foreach($stories as $story) {
                //dump($story);

                if($story->getToDate()->format('Y')=='9999') {
                    $actualManager = $manager;
                    break;
                }
            }
        }

        return $this->render('dept/show.html.twig', [
            'departement' => $department,
            'manager' => $actualManager,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dept_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Department $dept, DepartmentRepository $deptRepository): Response{
        $this->denyAccessUnlessGranted("ROLE_MANAGER");

        $form = $this->createForm(DeptType::class, $dept);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $deptRepository->save($dept, true);

                $this->addFlash('success','The department has been successfully updated.');

                return $this->redirectToRoute($request->headers->get('referer'), [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error','The department has not been updated.');
            }
        }

        return $this->renderForm('dept/edit.html.twig', [
            'department' => $dept,
            'form' => $form,
        ]);
    }
}
