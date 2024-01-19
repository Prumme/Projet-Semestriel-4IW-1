<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Quote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'identity',
                'attr' => [
                    "class"=>"input-50",
                    "icon"=> "people",
                    'placeholder' => 'Your firstname'
                ]
            ])
            ->add('add_customer',ButtonType::class,[
                'label' => 'Add a customer',
                'attr' =>[
                    "class"=> "input-50",
                ]
            ])
            ->add('emited_at',DateType::class,[
                'attr' => [
                    "class"=>"input-50",
                ]
            ])
            ->add('expired_at',DateType::class,[
                'attr' => [
                    "class"=>"input-50",
                ]
            ])
            ->add('has_been_signed')
            ->add('billingRows', CollectionType::class, [
                'entry_type' => BillingRowType::class,
                'label' => "Billing rows",
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}
