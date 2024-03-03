<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Company;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'attr'=>[
                    'icon'=>'pricetag',
                    'class'=>'input-50',
                    'placeholder'=>'Product Name'
                ]
            ])
            ->add('price',NumberType::class,[
                'attr'=>[
                    'class'=>'input-50',
                    'placeholder'=>'99.25'
                ]
            ])
            ->add('description',null,[
                'attr'=>[
                    'icon'=>'document-text',
                    'placeholder'=>'Product Description'
                ]
            ])
            ->add('categories', EntityType::class, [
                    'attr'=>[
                        'icon'=>'albums'
                    ],
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $repository) use ($options): QueryBuilder {
                            return $repository->createQueryBuilder('c')
                                ->where('c.company = :company')
                                ->setParameter('company', $options['company']);
                    },
                    'expanded' => false,
                    'multiple' => true,
                    'choice_label' => 'name',
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'company' => Company::class,
        ]);
    }
}
