<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\DeptType;
use App\Repository\DepartmentRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/departements')]
class DeptController extends AbstractController
{
    #[Route('/', name: 'app_dept_index')]
    public function index(DepartmentRepository $depts): Response
    {
        return $this->render('dept/index.html.twig', [
            'departements' => $depts->findAll(),
            'user' => $this->getUser()
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

                return $this->redirectToRoute('app_dept_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error','The department has not been updated.');
            }
        }

        return $this->renderForm('dept/edit.html.twig', [
            'department' => $dept,
            'form' => $form,
            'route' => 'edit',
        ]);
    }

    #[Route('/create', name: 'app_dept_create', methods: ['GET', 'POST'])]
    public function create(Request $request, Department $dept, DepartmentRepository $deptRepo): Response{
        $this->denyAccessUnlessGranted("ROLE_MANAGER");

        $form = $this->createForm(DeptType::class, $dept);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {

                //Trouver l'id du dernier departement
                $lastDept = $deptRepo->findBy([], ['id' => 'DESC'], 1, 0)[0]->getId();

                //enlever le prefixe et convertir le numero en int pour l'incrementer
                $id = (int)substr($lastDept, 1, strlen($lastDept));
                $id++;

                $id = (string)$id;

                if (strlen($id) == 2) {
                    $id = 'd0' . $id;
                } else if (strlen($id) == 3){
                    $id = 'd' . $id;
                }

                $dept->setId($id);
                $deptRepo->save($dept, true);

                dd('referrer -> ' . $request->get('referrer'));

                $this->addFlash('success','The department has been successfully created.');

                return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error','The department has not been updated.');
            }
        }

        return $this->renderForm('dept/edit.html.twig', [
            'department' => $dept,
            'form' => $form,
            'route' => 'create',
        ]);
    }

}
