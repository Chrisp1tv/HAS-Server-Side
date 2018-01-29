<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SearchType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, array(
                'label'    => 'form.label.search',
                'required' => true,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'search',
            ));
    }
}