<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'attr'=>[
                    'icon'=>'pricetag',
                    'class'=>'input-50',
                    'placeholder'=>'Category Name'
                ]
            ])
            ->add('products', EntityType::class, [
                    'attr'=>[
                        'class'=>'input-50',
                    ],
                    'class' => Product::class,
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use($options) {
                        return $er->createQueryBuilder('p')
                            ->where('p.company = :company')
                            ->setParameter('company', $options['company']);
                    },
                    'expanded' => false,
                    'multiple' => true,
                    'choice_label' => 'name',
                ]
            )
            ->add('description',null,[
                'attr'=>[
                    'icon'=>'document-text',
                    'placeholder'=>'Category Description'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'company'=>null,
        ]);
    }
}
