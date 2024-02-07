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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'query_builder' => function (CustomerRepository $repository) use ($options): QueryBuilder {
                    return $repository->createQueryBuilder('c')
                        ->where('c.company = :company')
                        ->setParameter('company', $options['company']);
                },
                'choice_label' => 'identity',
                'attr' => [
                    "class"=>"input-60",
                    "icon"=> "people",
                    'placeholder' => 'Your firstname'
                ]
            ])
            ->add('add_customer',ButtonType::class,[
                'label' => 'Add a customer',
                'attr' =>[
                    "class"=> "input-40",
                    "icon"=> "add-outline",
                ]
            ]);

        if(isset($options['customer'])){
            $builder->add('billingAddress', EntityType::class, [
                'label' => 'Billing address',
                'attr'=>[
                    'id' => 'billing_address',
                    'icon'=> 'home',
                ],
                'class' => BillingAddress::class,
                'choice_label' => 'fullAddress',
                'choices' => $options['customer']->getBillingAddresses(),
            ]);
        }else{
            $builder->add('billingAddress',HiddenType::class,[
                'data' => null,
                'mapped' => false,
            ]);
        }

        $builder->add('emited_at',DateType::class,[
            "label" => "Emited at",
            'attr' => [
                'icon'=> 'calendar',
                "class"=>"input-50",
            ]
        ])
            ->add('expired_at',DateType::class,[
                "label" => "Expired at",
                'attr' => [
                    'icon'=> 'calendar',
                    "class"=>"input-50",
                ]
            ])
            ->add('billingRows', CollectionType::class, [
                'entry_type' => BillingRowType::class,
                'entry_options' => [
                    'products' => $options['products'],
                ],
                'attr'=>[
                    'icon'=> 'list',
                ],
                'label' => "Billing rows",
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'products' => [],
            'customer' => null,
            'company' => null,
        ]);
    }
}
