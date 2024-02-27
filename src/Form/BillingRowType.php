<?php

namespace App\Form;

use App\Entity\BillingRow;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            "attr" => [
                "placeholder" => "Quantity",
            ],
            "label" => "Quantity",
        ]);
        $builder->add("unit", NumberType::class,[
            "attr" => [
                "placeholder" => "Unit price",
            ],
            "label" => "Unit",
        ]);
        $builder->add("price", NumberType::class,[
            'mapped' => false,
            "attr" => [
                "placeholder" => "Total price",
                "readonly" => true,
            ],
            "label" => "Total",
        ]);
        $builder->add("vat", NumberType::class,[
            "attr" => [
                "placeholder" => "VAT %",
            ],
            "label" => "VAT",
        ]);
        $builder->add("total", NumberType::class,[
            'mapped' => false,
            "attr" => [
                "placeholder" => "Total",
                "readonly" => true,
            ],
            "label" => "Total",
        ]);
        $builder->add("discount_type",ChoiceType::class,[
            'choices' => [
                'Percentage' => 1,
                'Fixed' => 2,
            ],
            'label' => 'Discount Type',
            'mapped' => true,
            'required' => false,
        ]);
        $builder->add("discount_value", TextType::class,[
            'mapped' => true,
            'label' => 'Discount value',
            "attr" => [
                "placeholder" => "Discount value",
            ],
            'required' => false,
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
