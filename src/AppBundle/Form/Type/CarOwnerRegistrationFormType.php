<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class CarOwnerRegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => 'car_owner.first_name'))
            ->add('lastName', 'text', array('label' => 'car_owner.last_name'))
            ->add('nickname', 'text', array('label' => 'car_owner.nickname'))
            ->add('gender', 'choice', array(
                'label' => 'car_owner.gender',
                'choices' => array('male' => 'Мужской', 'female' => 'Женский'),
                'required' => false,
                'empty_value' => 'Выберите ваш пол',
                'empty_data' => null
            ))
            ->add('birthday', 'birthday', array('label' => 'car_owner.birthday', 'years' => range(date('Y')-10, date('Y')-80)))
            ->add('locality', 'autocomplete', array(
                        'label' => 'address.locality',
                        'class' => 'AppBundle:Locality',
                        'empty_value' => 'address.choose_locality',
                        'property' => 'name',
                        'choices' => array(),
                        'required' => true
                ))
            ->add('user', new UserRegistrationFormType(), array('user_type' => 'car_owner'));

        if ($options['captcha']) {
            $builder
                ->add('captcha', 'captcha', array(
                    'label' => 'registration.captcha'
                ));
        }

        $em = $options['em'];

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['locality']) && $data['locality']) {
                $q = $em->createQuery('select l from AppBundle:Locality l where l.id = :locality')
                    ->setParameter('locality', $data['locality']);

                $form->add('locality', 'autocomplete', array(
                        'label' => 'address.locality',
                        'class' => 'AppBundle:Locality',
                        'empty_value' => 'address.choose_locality',
                        'property' => 'name',
                        'choices' => $q->getResult(),
                        'required' => true
                ));
            }

        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($em) {
            $form = $event->getForm();
            $data = $event->getData();

            $form->add('locality', 'autocomplete', array(
                    'label' => 'address.locality',
                    'class' => 'AppBundle:Locality',
                    'empty_value' => 'address.choose_locality',
                    'property' => 'name',
                    'choices' => $data->getLocality() ? array($data->getLocality()) : array(),
                    'required' => false,
            ));

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CarOwner',
            'validation_groups' => array('Default', 'password'),
            'em' => null,
            'captcha' => true
        ));
    }

    public function getName()
    {
        return 'car_owner_registration';
    }

}