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

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country_code')
            ->add('city')
            ->add('zip_code')
            ->add('address_line_1')
            ->add('address_line_2', null, ['required' => false])
            ->add('name')
            ->add('siret')
            ->add('var_number');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // $resolver->setDefaults([
        //     'data_class' => User::class,
        // ]);
    }
}
