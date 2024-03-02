<?php

namespace App\Form;

use App\Entity\BillingAddress;
use App\Entity\Customer;
use App\Entity\Quote;
use App\Repository\CustomerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Data\QuoteSearch;

class QuoteFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('status', ChoiceType::class,
            [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Draft' => 'draft',
                    'Signed' => 'signed',
                ],
                'placeholder' => 'Choose a status',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filter',
                'attr' => [
                    'icon'=> 'filter'
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuoteSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'company' => null
        ]);
    }
    
}
