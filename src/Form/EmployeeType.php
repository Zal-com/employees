<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('birth_date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('gender',
                ChoiceType::class, [
                    'choices'  => [
                        'F' => 'F',
                        'M' => 'M',
                        'X' => 'X',
                    ],
                    'invalid_message' => 'Valeur incorrecte!',
                ])
            ->add('photo', FileType::class, [
                'data_class' => null,
                'required' => false,
                'attr' => ['accept' => 'image/png, image/jpeg']
            ])
            ->add('email', EmailType::class)
            ->add('hire_date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('departments', EntityType::class, [
                'class' => Department::class,
                'multiple' => true,
                'label' => 'Departement : ',
                'choice_label' => 'dept_name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
