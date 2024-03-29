<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\DeptEmp;
use App\Entity\Employee;
use App\Form\EmployeePasswordType;
use App\Form\EmployeePicType;
use App\Form\EmployeeType;
use App\Repository\DepartmentRepository;
use App\Repository\DeptEmpRepository;
use App\Repository\EmployeeRepository;
use ContainerBfGMnRY\getEmployeeControllerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use function Sodium\randombytes_random16;

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
                if (!empty($form->get('photo')->getData())) {
                    $file = $form->get('photo')->getData();
                    $file->move('../public/images/employee', $file->getClientOriginalName());

                    $employee->setPhoto('employee/' . $file->getClientOriginalName());
                }

                //Récupération de la derniere position de l'employé
                $lastPosition = $deptEmpRepo->findBy(['employee' => $employee->getId()])[0];

                //Modification du DeptEmp actuel si changement
                if($lastPosition->getDepartment()->getId() != $employee->getDepartments()[0]->getId()){
                    $lastPosition->setToDate(new \DateTime('now'));
                    $deptEmpRepo->save($lastPosition, true);
                }


                //Creation du nouveau DeptEmp
                $newDeptEmp = new DeptEmp();
                $newDeptEmp->setEmployee($employee);
                $newDeptEmp->setDepartment($employee->getDepartments()->last());
                $newDeptEmp->setFromDate(new \DateTime('now'));
                $newDeptEmp->setToDate(new \DateTime('31-12-9999'));


                $deptEmpRepo->save($newDeptEmp, true);

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
                        UserPasswordHasherInterface $passwordHasher,
                        MailerInterface $mailer,
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
            $random = md5(rand(10, 10));

            $employee->setPassword($passwordHasher->hashPassword($employee, $random));

            //Creation d'un nouveau DeptEmp avec le nouveau département
            $dept = new DeptEmp();
            $dept->setEmployee($employee);
            $dept->setFromDate(new \DateTime('now'));
            $dept->setToDate(new \DateTime('31-12-9999'));
            $dept->setDepartment($deptRepo->find($request->get('employee')['departments'][0]));

            $employeeRepository->save($employee, true);
            $deptEmpRepo->save($dept, true);

            //Envoi du mail avec les credentials
            //redirect vers mailer, puis redirect en fonction du referrer
            self::sendCreateMail($employee, $random);

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

    #[Route('/profile/{id}/changePassword', name: 'app_employee_changepassword', methods: ['GET', 'POST'])]
    public function changePassword(EmployeeRepository $employeeRepo, Request $request, Employee $employee ) : Response{
        //User a besoin d'etre authentifié pour accéder à la page
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        $userId = $this->getUser()->getId();

        //User ne peut modifier que son propre mot de passe
        if($userId != $request->get('id')){
            return $this->redirectToRoute('app_employee_profile', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(EmployeePasswordType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee->setPassword(password_hash($form->get('plainPassword')->getViewData()['first'], PASSWORD_BCRYPT));
            $employeeRepo->save($employee, true);

            return $this->redirectToRoute('app_employee_profile', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/changePassword.html.twig', [
            'employee' => $this->getUser(),
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/editPicture', name: 'app_employee_editpicture', methods: ['GET', 'POST'])]
    public function editPicture(EmployeeRepository $employeeRepo, Employee $employee, Request $request) : Response {
        //User a besoin d'etre authentifié pour accéder à la page
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        $userId = $this->getUser()->getId();

        //User ne peut modifier que son propre mot de passe
        if($userId != $request->get('id')){
            return $this->redirectToRoute('app_employee_profile', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(EmployeePicType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            $file->move('../public/images/employee', $file->getClientOriginalName());

            $employee->setPhoto('employee/' . $file->getClientOriginalName());
            $employeeRepo->save($employee, true);

            return $this->redirectToRoute('app_employee_profile', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/changePicture.html.twig', [
            'employee' => $this->getUser(),
            'form' => $form,
        ]);
    }

    private function sendCreateMail(Employee $employee, $password){

        $password = utf8_encode($password);

        $transport = Transport::fromDsn('smtp://zalcom69@gmail.com:xonfuupggibhvzjb@smtp.gmail.com:587');

        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('employees@test.com')
            ->to($employee->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Bienvenu.e chez nous !')
            ->html("<h1>Bienvenu.e chez nous!</h1>
                <div>
                    <p>Vos identifiants de connexion au portail des employés ont été créés.</p>
                    <p>Nous vous conseillons de vous rendre sans plus attendre sur la plateforme afin de modifier votre mot de passe</p>
                    <ul>Vos identifiants :
                    <li>E-mail : {$employee->getEmail()}</li>
                    <li>Mot de passe : {$password}</li>
                    </ul>
                    </div>");

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo print_r($e, TRUE);
        }

    }
}