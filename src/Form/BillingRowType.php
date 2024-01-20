<?php

namespace App\Form;

use App\Entity\BillingRow;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add("product");
        $builder->add("quantity", NumberType::class,[
            'attr' => [
                "class"=>"input-10",
            ]
        ]);
        $builder->add("unit", NumberType::class,[
            'attr' => [
                "class"=>"input-10",
            ]
        ]);
        $builder->add("price", NumberType::class,[
            'attr' => [
                "class"=>"input-10",
            ]
        ]);
        $builder->add("vat", NumberType::class,[
            'attr' => [
                "class"=>"input-10",
            ]
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['products'] = $options['products'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingRow::class,
            'products' => [],
        ]);
    }
}
