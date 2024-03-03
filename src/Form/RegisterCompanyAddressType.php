<?php

namespace App\Form;

use App\Entity\BillingAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegisterCompanyAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country_code',null,[
                'label' => 'Country Code',
                'required'=>true,
                'attr'=>[
                    'icon'=>'flag-outline',
                    'placeholder'=>'FR',
                    'class'=>'input-50'
                ]
            ])
            ->add('zip_code',null,[
                'label' => 'Zip code',
                'required'=>true,
                'attr'=>[
                    'icon'=>'calculator-outline',
                    'placeholder'=>'75 012',
                    'class'=>'input-50'
                ]
            ])
            ->add('city',null,[
                'label' => 'City',
                'required'=>true,
                'attr'=>[
                    'icon'=>'business-outline',
                    'placeholder'=>'Paris'
                ]
            ])
            ->add('address_line_1',null,[
                'label' => 'Address line 1',
                'required'=>true,
                'attr'=>[
                    'icon'=>'home-outline',
                    'placeholder'=>'2 rue des lilas',
                    'class'=>'input-50'
                ]
            ])
            ->add('address_line_2',null,[
                'label' => 'Address line 2',
                'required'=>true,
                'attr'=>[
                    'icon'=>'locate-outline',
                    'placeholder'=>'(optional)',
                    'class'=>'input-50'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
             'data_class' => BillingAddress::class,
        ]);
    }
}
