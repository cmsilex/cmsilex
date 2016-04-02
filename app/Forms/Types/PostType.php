<?php

namespace CMSilex\Forms\Types;

use CMSilex\Entities\Post;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends PageType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }
}