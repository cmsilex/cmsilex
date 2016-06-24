<?php

namespace CMSilex\Forms\Types;

use CMSilex\Entities\Page;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('created', DateTimeType::class)
            ->add('subtitle', TextType::class, [
                'required' => false
            ])
            ->add('slug')
            ->add('parentPage', EntityType::class, [
                'class' => Page::class,
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => '---',
                'query_builder' => function (EntityRepository $repo) use ($options)
                {
                    $qb = $repo->createQueryBuilder('p');

                    if (isset($options['data']))
                    {
                        $qb->where($qb->expr()->neq('p.id', ':id'))->setParameter('id', $options['data']->getId());
                    }

                    return $qb;
                }
            ])
            ->add('content', TextareaType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class
        ]);
    }
}