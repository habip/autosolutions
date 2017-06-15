<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'repeated', array(
                    'first_options' => array('label' => 'registration.password'),
                    'second_options' => array('label' => 'registration.password_repeat'),
                    'type' => 'password',
                    'invalid_message' => 'registration.password_repeat',
                    'required' => true,
            ))
            ->add('save', 'submit');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'validation_groups' => array('password')
        ));
    }

    public function getName()
    {
        return 'resetting';
    }

}