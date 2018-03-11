<?php

namespace App\Form;

use App\Entity\Recipient;
use App\Entity\RecipientGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CampaignType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'    => 'name',
                'required' => true,
            ))
            ->add('sendingDate', DateTimeType::class, array(
                'label'       => 'form.label.sendingDate',
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
                'required'    => true,
            ))
            ->add('message', MessageType::class, array(
                'label'    => false,
                'required' => true,
            ))
            ->add('sendToAllRecipients', CheckboxType::class, array(
                'label'    => 'form.label.sendToAllRecipients',
                'required' => false,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $campaign = $event->getData();
                $form = $event->getForm();

                if (null === $campaign or null != $campaign->getId()) {
                    $form->remove('sendToAllRecipients');
                }
            })
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
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.label.save',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Campaign',
        ));
    }
}
