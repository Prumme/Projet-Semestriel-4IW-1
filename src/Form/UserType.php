<?php

namespace App\Form;

use App\Entity\User;
use App\Security\AuthentificableRoles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles',  ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => AuthentificableRoles::ROLE_USER,
                    'ROLE_COMPANY_ADMIN' => AuthentificableRoles::ROLE_COMPANY_ADMIN,
                ],
                'multiple' => true,
                'placeholder' => 'Choose a role',
            ])
            ->add('firstname')
            ->add('lastname');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
