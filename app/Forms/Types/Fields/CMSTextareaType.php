<?php

namespace CMSilex\Forms\Types\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class CMSTextareaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('val', TextareaType::class, [
            'label' => false
        ]);
    }

    public function getParent()
    {
        return CMSTextType::class;
    }
}