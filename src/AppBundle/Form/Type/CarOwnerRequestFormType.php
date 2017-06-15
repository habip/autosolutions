<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use AppBundle\Entity\CarOwnerRequest;

class CarOwnerRequestFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!in_array($options['type'], array('user_create_request', 'anon_create_request'))) {
            throw new InvalidArgumentException('wrong value of type option');
        }

        $builder
            ->add('carOwnerDate', 'date', array(
                'label' => 'Выберите удобную дату заезда',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ))
            ->add('carOwnerTimePeriod', 'choice', array(
                'label' => 'Выберите удобное для вас время',
                'choices' => array(
                        1 => 'Первая половина дня',
                        2 => 'Вторая половина дня'
                )
            ))
            ->add('services', 'entity', array(
                'class' => 'AppBundle:Service',
                'by_reference' => true,
                'property' => 'name',
                'required' => false,
                'multiple' => true
            ))
        ;

        if ('anon_create_request' == $options['type']) {
            $builder
                ->add('phone', 'text')
                ->add('car', new CarFormType(), array('em' => $options['em'], 'basic_data_only' => true))
                ->add('carOwner', new CarOwnerFormType(), array(
                    'em' => $options['em'],
                    'embed_parent' => 'CarOwnerRequest',
                    'cascade_validation' => true
                ))
                ->add('description', 'textarea', array(
                    'required' => false,
                    'label' => 'Дополнительная информация'
                ))
            ;
        } else if ('user_create_request' == $options['type']) {
            if ($options['carOwner']){
                $cars = $options['em']->getRepository('AppBundle:Car')->findByCarOwner($options['carOwner']);

                $builder->add('car', 'entity', array(
                        'class' => 'AppBundle:Car',
                        'by_reference' => true,
                        'property' => 'fullname',
                        'multiple' => false,
                        'choices' => $cars
                ));
            }
        }

        $em = $options['em'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options){
            $form = $event->getForm();
            $carOwnerRequest = $event->getData();

            if (($carOwnerRequest && $carOwnerRequest->getCarService() && $carOwnerRequest->getCarService()->isSchedulable()
                    || $options['carService'] && $options['carService']->isSchedulable())) {
                $form
                    ->add('carOwnerDate', 'datetime', array(
                        'widget' => 'single_text',
                        'required' => false
                    ))
                    ->remove('carOwnerTimePeriod')
                ;
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em, $builder, $options) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['carService'])) {

                $q = $em->createQuery('select c from AppBundle:CarService c where c.id = :id')->setParameter('id', $data['carService']);

                $form->add('carService', 'entity', array(
                        'class' => 'AppBundle:CarService',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => false,
                        'choices' => $q->getResult()
                ));
            }

            if (isset($data['services']) && is_array($data['services'])) {
                $services = $data['services'];

                $form->add('services', 'entity', array(
                        'class' => 'AppBundle:Service',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => true,
                        'choices' => $em->getRepository('AppBundle:Service')->findById($services)
                ));
            }

            $carOwnerRequest = $builder->getData();

            if (($carOwnerRequest && $carOwnerRequest->getCarService() && $carOwnerRequest->getCarService()->isSchedulable()
                    || $options['carService'] && $options['carService']->isSchedulable())) {
                $form
                    ->add('carOwnerDate', 'datetime', array(
                        'widget' => 'single_text',
                        'attr' => array('style' => 'visibility: hidden'),
                        'required' => false
                    ))
                    ->remove('carOwnerTimePeriod');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CarOwnerRequest',
            'cascade_validation' => true,
            'em' => null,
            'carOwner' => null,
            'carService' => null,
            'type' => 'user_create_request',
        ));
    }

    public function getName()
    {
        return '';
    }
}