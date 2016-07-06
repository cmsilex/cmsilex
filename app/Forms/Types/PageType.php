<?php

namespace CMSilex\Forms\Types;

use CMSilex\Components\ThemeComponent;
use CMSilex\Entities\Page;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PageType extends AbstractType
{
    protected $themeComponent;

    public function __construct(ThemeComponent $themeComponent)
    {
        $this->themeComponent = $themeComponent;
    }

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
            ->add('content', TextareaType::class, ['required' => false, 'attr' => ['class' =>'ckeditor']])
            ->add('template', TemplateChoiceType::class)
        ;

        $fieldsForm = $builder->create('fields', FormType::class, [
                'by_reference' => false,
                'mapped' => true,
                'label' => false,
            ])
        ;

        $builder->add($fieldsForm);

        $templateName = $options['data']->getTemplate();

        if ($templateName)
        {
            $template = $this->themeComponent->getTemplate($templateName);
            $fields = $template['fields'] ? $template['fields'] : [];

            foreach ($fields as $currentField)
            {
                $fieldsForm->add(
                    $currentField['name'],
                    'CMSilex\Forms\Types\Fields\\CMS' . ucwords($currentField['type']) . 'Type'
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class
        ]);
    }
}