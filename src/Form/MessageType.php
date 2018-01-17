<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MessageType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, array(
                'label'    => 'form.label.content',
                'required' => true,
            ))
            ->add('color', TextType::class, array(
                'label' => 'form.label.color',
                'required' => false,
            ))
            ->add('bold', CheckboxType::class, array(
                'label' => 'form.label.bold',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.label.save',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Message',
        ));
    }
}