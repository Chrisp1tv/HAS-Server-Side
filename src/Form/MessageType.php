<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('endDate', DateTimeType::class, array(
                'label'       => 'form.label.endDate',
                'widget'      => 'choice',
                'years'       => range(date('Y') - 1, date('Y') + 1),
                'placeholder' => array(
                    'day'   => 'day',
                    'month' => 'month',
                    'year'  => 'year',
                ),
                'attr'        => array(
                    'class' => 'container-inline-fields',
                ),
                'required'    => false,
            ))
            ->add('repetitionFrequency', IntegerType::class, array(
                'label'    => 'form.label.repetitionFrequency',
                'attr'     => array(
                    'placeholder' => 'form.placeholder.repetitionFrequency',
                ),
                'required' => false,
            ))
            ->add('content', TextareaType::class, array(
                'label'    => 'form.label.content',
                'required' => true,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Message',
        ));
    }
}
