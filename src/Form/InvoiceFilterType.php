<?php

namespace App\Form;

use App\Data\InvoiceSearch;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Repository\CustomerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceFilterType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InvoiceSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'company' => null
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Customer::class,
                'query_builder' => function (CustomerRepository $repository) use ($options): QueryBuilder {
                    return $repository->createQueryBuilder('c')
                        ->where('c.company = :company')
                        ->setParameter('company', $options['company']);
                },
                'placeholder' => 'Choose a customer'
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Awaiting payment' => Invoice::STATUS_AWAITING_PAYMENT,
                    'Paid' => Invoice::STATUS_PAID,
                    'Unpaid' => Invoice::STATUS_UNPAID,
                    'Cancelled' => Invoice::STATUS_CANCELLED
                ]
            ])
            ->add('quote', HiddenType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filter',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);

    }

}