<?php

namespace App\Form;

use App\Entity\Quote;
use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Choice;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceNumber', TextType::class, [
                "label"=>"Invoice Number",
                'required' => false,
                'attr'=>[
                    'readonly' => true,
                    'disabled' => true,
                    'icon'=> 'cube'
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Payment Status',
                'required' => false,
                'choices' => [
                    'Awaiting Payment' => Invoice::STATUS_AWAITING_PAYMENT,
                    'Paid' => Invoice::STATUS_PAID,
                    'Unpaid' => Invoice::STATUS_UNPAID,
                    'Cancelled' => Invoice::STATUS_CANCELLED,
                ],
            ])
            ->add('emitted_at', DateTimeType::class, [
                'label' => 'Emited Date',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'icon'=> 'calendar',
                    "class"=>"input-50",
                ]
            ])
            ->add('expired_at', DateTimeType::class, [
                'label' => 'Expired At',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    "class"=>"input-50",
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
