<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RecipientType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'    => 'form.label.name',
                'required' => true,
            ))
            ->add('linkingIdentifier', TextType::class, array(
                'label'    => 'form.label.linkingIdentifier',
                'disabled' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Recipient',
        ));
    }
}