<?php

namespace CMSilex\Forms\Types\Fields;

use CMSilex\Forms\Types\CMSFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CMSTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('val', TextType::class, [
            'label' => false
        ]);
    }

    public function getParent()
    {
        return CMSFieldType::class;
    }
}