<?php

namespace App\Form;

use App\Entity\CleaningItemCategory;
use App\Form\Model\ToggleableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CleaningItemCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'resource.fields.title'])
            ->add('displayOrder', NumberType::class, ['label' => 'cleaningItemCategory.fields.displayOrder'])
            ->add('toggleable', ToggleableType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CleaningItemCategory::class,
        ]);
    }
}
