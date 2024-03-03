<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('firstname',null,[
            'label' => 'Firstname',
            'attr' => [
                'icon'=>'people',
                'class'=>'input-50',
                'placeholder' => 'Enter your firstname'
            ]
        ])
            ->add('lastname',null,[
                'label' => 'Lastname',
                'attr' => [
                    'class'=>'input-50',
                    'placeholder' => 'Enter your lastname'
                ]
            ])
        ->add('email',EmailType::class,[
            'label' => 'Email',
            'attr' => [
                'icon'=>'at',
                'placeholder' => 'Enter your email'
            ]
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'label' => 'Password',
            'first_options'  => [
                'label' => 'New Password',
                'required' => true,
                'attr' => [
                    'class'=>'input-50',
                    'placeholder' => 'Enter your password',
                    'icon'=>'lock-closed-outline'
                ]
            ],
            'second_options' => [
                'label' => 'Password Verification',
                'required' => true,
                'attr' => [
                    'class'=>'input-50',
                    'placeholder' => 'Enter your password again',
                    'icon'=>'lock-closed-outline'
                ]
            ],
        ])
        ->add('submit',SubmitType::class,[
            'label' => 'Register',
            'attr' => [
                'icon'=>'add-circle-outline',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
