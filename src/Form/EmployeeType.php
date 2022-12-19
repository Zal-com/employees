<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
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
            ->add('birth_date')
            ->add('gender',
                ChoiceType::class, [
                    'choices'  => [
                        'F' => 'F',
                        'M' => 'M',
                        'X' => 'X',
                    ],
                    'invalid_message' => 'Valeur incorrecte!',
                ])
            ->add('photo')
            ->add('email')
            ->add('hire_date')
            //->add('departments')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
