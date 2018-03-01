<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AdministratorType
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class AdministratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label'    => 'username',
                'required' => true,
            ))
            ->add('password', PasswordType::class, array(
                'label'    => 'password',
                'required' => true,
            ))
            ->add('disabled', CheckboxType::class, array(
                'label'    => 'disabled',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.label.save',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Administrator',
        ));
    }
}