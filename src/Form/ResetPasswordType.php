<?php

namespace App\Form;

use App\Entity\User;
use App\Security\AuthentificableRoles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Password',
                'first_options'  => ['label' => 'New Password', 'required' => true],
                'second_options' => ['label' => 'Password Verification', 'required' => true],
                ])
        ;
    }
}
