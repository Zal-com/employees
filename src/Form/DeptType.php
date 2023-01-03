<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\DeptEmp;
use App\Repository\EmployeeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Employee;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class DeptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $deptEmps = $options['data']->getDeptEmps();
       $employees = [];

        foreach ($deptEmps as $employee) {
            array_push($employees, $employee->getId());
       }

        $builder
            ->add('deptName', TextType::class, ['label' => 'Nom du département : '])
            ->add('description', TextType::class, ['label' => 'Description : '])
            ->add('address', TextType::class, ['label' => 'Adresse : '])
            ->add('roi_url', UrlType::class, ['label' => 'Lien du ROI : '])
            ->add('managers', EntityType::class, [
                'label' => 'Manager : ',
                'class' => Employee::class,
                'multiple' => true,
                    ]
            //->add('departments')
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}
