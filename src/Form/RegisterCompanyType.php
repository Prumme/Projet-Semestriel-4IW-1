<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'label' => 'Name of the company',
                'required'=>true,
                'attr'=>[
                    'icon'=>'flag-outline',
                    'placeholder'=>'Name'
                ]
            ])
            ->add('siret',null,[
                'label' => 'Siret',
                'required'=>true,
                'attr'=>[
                    'icon'=>'at',
                    'placeholder'=>'Siret Number'
                ]
            ])
            ->add('vat_number',null,[
                'label' => 'VAT Number',
                'required'=>true,
                'attr'=>[
                    'icon'=>'document-outline',
                    'placeholder'=>'Vat Number'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
             'data_class' => Company::class,
        ]);
    }
}
