<?php

namespace CMSilex\Forms\Types\Fields;

use Symfony\Component\Form\AbstractType;

class ImageType extends AbstractType
{
    public function getParent()
    {
        return FileType::class;
    }
}