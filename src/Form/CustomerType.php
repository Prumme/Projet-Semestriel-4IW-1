<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add(
                'company_name',
                null,
                [
                    'label' => 'Company Name',
                ]
            )
            ->add('company_siret', null, [
                'label' => 'Company Siret'
            ])
            ->add('company_vat_number', null, [
                'label' => 'Company VAT Number'
            ])
            ->add('email')
            ->add('tel');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
