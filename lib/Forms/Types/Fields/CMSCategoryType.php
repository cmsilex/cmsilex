<?php

namespace CMSilex\Forms\Types\Fields;

use CMSilex\Entities\Category;
use CMSilex\Entities\CMSCategoryField;
use CMSilex\Forms\Types\CMSFieldType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CMSCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('category', EntityType::class,[
            'class' => Category::class,
            'placeholder' => "---",
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CMSCategoryField::class
        ]);
    }

    public function getParent()
    {
        return CMSFieldType::class;
    }
}