<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use App\Repository\DeptManagerRepository;
use App\Repository\EmployeeRepository;
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
            'employee' => $this->getUser()
        ]);
    }
    #[Route('/{id}', name:'app_dept_show', methods: ['GET'])]
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
}
