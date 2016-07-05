<?php

namespace CMSilex\Forms\Types;

use CMSilex\Components\ThemeComponent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateChoiceType extends AbstractType
{
    protected $themeComponent;

    public function __construct(ThemeComponent $themeComponent)
    {
        $this->themeComponent = $themeComponent;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $templates = $this->themeComponent->getTemplates();

        $templateChoices = array_combine(array_keys($templates), array_keys($templates));

        $resolver->setDefault('choices', $templateChoices);

        $resolver->setDefault('empty_data', reset($templates));

    }

    public function getParent()
    {
        return ChoiceType::class;
    }


}