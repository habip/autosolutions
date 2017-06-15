<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['email']) {
            $builder
                ->add('email', 'email', array(
                        'label' => 'Email',
                        'required' => $options['user_type'] == 'car_owner' || $options['user_type'] == 'agent' ? false : true
                ));
        }

        if ($options['username']) {
            $builder->add('username', 'text', array('label' => 'Имя пользователя'));
        }

        $builder
            ->add('plainPassword', 'repeated', array(
                    'first_options' => array('label' => 'registration.password'),
                    'second_options' => array('label' => 'registration.password_repeat'),
                    'type' => 'password',
                    'invalid_message' => 'registration.password_repeat',
                    'required' => true,
            ))
            ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $user = $event->getData();

            if ($user && $user->getId()) {
                $form
                    ->add('plainPassword', 'repeated', array(
                            'first_options' => array('label' => 'Введите новый пароль если хотите его заменить'),
                            'second_options' => array('label' => 'registration.password_repeat'),
                            'type' => 'password',
                            'invalid_message' => 'registration.password_repeat',
                            'required' => false,
                    ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'validation_groups' => array('Default', 'password'),
                'user_type' => 'unknown',
                'email' => true,
                'username' => false
        ));
    }

    public function getName()
    {
        return 'user_registration';
    }

}