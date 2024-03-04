<?php

namespace App\Form;

use App\Data\ContactUsDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactUsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
             'data_class' => ContactUsDTO::class,
         ]);
    }
}
