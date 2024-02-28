<?php

namespace App\Form;

use App\Entity\Quote;
use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'required' => false,
            ])
            ->add('status', TextType::class, [
                'required' => false,
            ])
            ->add('emitted_at', DateTimeType::class, [
                'required' => false,
            ])
            ->add('expired_at', DateTimeType::class, [
                'required' => false,
            ])
            ->add('quote', EntityType::class, [
                'class' => Quote::class,
                'choice_label' => 'id',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
