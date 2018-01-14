<?php

namespace App\Form;

use App\Entity\Recipient;
use App\Entity\RecipientGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CampaignType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'    => 'form.label.name',
                'required' => true,
            ))
            ->add('message', MessageType::class, array(
                'label'    => false,
                'required' => true,
            ))
            ->add('sendingDate', DateTimeType::class, array(
                'label'    => 'form.label.sendingDate',
                'required' => true,
            ))
            ->add('endDate', DateTimeType::class, array(
                'label'    => 'form.label.endDate',
                'required' => false,
            ))
            ->add('repetitionFrequency', IntegerType::class, array(
                'label'       => 'form.label.repetitionFrequency',
                'placeholder' => 'form.placeholder.repetitionFrequency',
                'required'    => false,
            ))
            ->add('recipients', EntityType::class, array(
                'class'    => Recipient::class,
                'label'    => 'form.label.recipients',
                'multiple' => true,
                'required' => false,
            ))
            ->add('recipientGroups', EntityType::class, array(
                'class'    => RecipientGroup::class,
                'label'    => 'form.label.recipientGroups',
                'multiple' => true,
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Campaign',
        ));
    }
}