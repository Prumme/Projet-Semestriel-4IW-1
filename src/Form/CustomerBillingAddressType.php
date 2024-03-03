<?php

namespace App\Form;

use App\Entity\BillingAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerBillingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country_code',null,[
                'label'=>'Country Code',
                'attr'=>[
                    'icon'=>'flag',
                    'class'=>'input-50',
                    'placeholder'=>'Country Code'
                ]
            ])
            ->add('zip_code',null,[
                'label'=>'Zip Code',
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'Zip Code'
                ]
            ])
            ->add('city',null,[
                'label'=>'City',
                'attr'=>[
                    'icon'=>'business',
                    'placeholder'=>'City'
                ]
            ])

            ->add('address_line_1',null,[
                'label'=>'Address Line 1',
                'attr'=>[
                    'icon'=>'home',
                    'class'=>'input-50',
                    'placeholder'=>'Address Line 1'
                ]
            ])
            ->add('address_line_2',null,[
                'label'=>'Address Line 2',
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'Address Line 2'
                ]
            ])




        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingAddress::class,
        ]);
    }
}
