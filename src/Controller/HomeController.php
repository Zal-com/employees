<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    public function download() : BinaryFileResponse
    {
        $file = new SplFileInfo('rapport.pdf', __DIR__ . '/../public/rapports/rapport.pdf', 'path');
        $response = new BinaryFileResponse($file);

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', $file->getMimeType());
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getName()
        ));

        return $response;
    }
}
