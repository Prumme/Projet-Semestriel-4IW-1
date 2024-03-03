<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class,[
                'attr'=>[
                    'icon'=>'person',
                    'class'=>'input-50',
                    'placeholder'=>'Doe',
                ],
            ])
            ->add('firstname',null,[
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'John'
                ],
            ])
            ->add('email',null,[
                'attr'=>[
                    'icon'=>'at-circle',
                    'placeholder'=> 'john.doe@email.com'
                ]
            ])
            ->add('tel',null,[
                'label'=>'Phone Number',
                'attr'=>[
                    'icon'=>'call',
                    'placeholder'=>'0123456789'
                ]
            ])
            ->add(
                'company_name',
                null,
                [
                    'label' => 'Company Name',
                    'attr'=>[
                        'icon'=>'business',
                        'class'=>'input-50',
                        'placeholder'=>'Google'
                    ]
                ]
            )
            ->add('company_siret', null, [
                'label' => 'Company Siret',
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'12345678912345'
                ]
            ])
            ->add('company_vat_number', null, [
                'label' => 'Company VAT Number',
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'FR12345678912345'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
