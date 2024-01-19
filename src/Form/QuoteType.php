<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Quote;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityType::class, [
                // looks for choices from this entity
                'class' => Customer::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'identity',
                'attr' => [
                    "class"=>"input-50", // take 50% of the width of the row,
                    "icon"=> "people", // io-icon
                    'placeholder' => 'Your firstname'
                ]
            ])
            ->add('add_customer',ButtonType::class,[
                'attr' =>[
                    "class"=> "input-50",
                ]
            ])
            ->add('emited_at')
            ->add('expired_at')
            ->add('has_been_signed')
            ->add('lines', CollectionType::class, [
                // each entry in the array will be an "email" field
                'entry_type' => TextType::class,
                // these options are passed to each "email" type
                'entry_options' => [
                    'attr' => ['class' => 'email-box'],
                ],
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
