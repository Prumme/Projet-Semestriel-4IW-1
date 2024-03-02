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
            ->add('address_line_1',null,[
                'attr'=>[
                    'class'=>'input-50'
                ]
            ])
            ->add('city',null,[
                'attr'=>[
                    'class'=>'input-50'
                ]
            ])
            ->add('address_line_2',null,[
                'attr'=>[
                    'class'=>'input-50'
                ]
            ])
            ->add('zip_code',null,[
                'attr'=>[
                    'class'=>'input-50'
                ]
            ])
            ->add('country_code',null,[
                'attr'=>[
                    'class'=>'input-50'
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
