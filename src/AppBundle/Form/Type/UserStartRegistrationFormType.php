<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserStartRegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', 'tel', array('label' => 'Введите номер телефона'));
        if ($options['email']) {
            $builder->add('email', 'email', array('required' => false, 'label' => 'Email'));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'validation_groups' => array('start_registration'),
                'email' => false
        ));
    }

    public function getName()
    {
        return 'user_start_registration';
    }
}