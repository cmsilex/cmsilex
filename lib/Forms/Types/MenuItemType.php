<?php

namespace CMSilex\Forms\Types;

use CMSilex\Entities\BlogItem;
use CMSilex\Entities\MenuItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true
            ])
            ->add('blogItem', EntityType::class, [
                'class' => BlogItem::class,
                'choice_label' => 'title',
                'placeholder' => '---',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
            'label' => false
        ]);
    }
}