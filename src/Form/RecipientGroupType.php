<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RecipientGroupType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'    => 'form.label.name',
                'required' => true,
            ))
            ->add('recipients', EntityType::class, array(
                'label'    => 'form.label.recipients',
                'multiple' => true,
                'required' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\RecipientGroup',
        ));
    }
}