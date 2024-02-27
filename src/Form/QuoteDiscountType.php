<?php

namespace App\Form;

use App\Entity\QuoteDiscount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteDiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('discount_type', HiddenType::class,[
            "empty_data" => "1",
        ]);
        $builder->add('discount_value', TextType::class,[
            'label' => 'Discount percentage',
            'required' => false,
            'attr'=>[
                'placeholder'=> "50%",
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteDiscount::class,
        ]);
    }
}
