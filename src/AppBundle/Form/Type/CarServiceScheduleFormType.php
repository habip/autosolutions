<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use AppBundle\Entity\CarServiceSchedule;

class CarServiceScheduleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $builder
            ->add('post', 'entity', array(
                'class' => 'AppBundle:CarServicePost',
                'by_reference' => true,
                'property' => 'name',
                'required' => false,
                'multiple' => false,
                'choices' => array()
            ))
            ->add('startTime', 'datetime', array(
                'label' => 'Время заезда',
                'widget' => 'single_text',
                'format' => 'yyyy.MM.dd HH:mm:ss'
            ))
            ->add('endTime', 'datetime', array(
                'label' => 'Время выезда',
                'widget' => 'single_text',
                'format' => 'yyyy.MM.dd HH:mm:ss'
            ))
        ;

        if ($options['carOwnerRequest']) {
            $builder->add('carOwnerRequest', 'entity', array(
                'class' => 'AppBundle:CarOwnerRequest',
                'by_reference' => true,
                'property' => 'name',
                'required' => false,
                'multiple' => false,
                'choices' => array()
            ));
        }

        function getCarService($data, $options)
        {
            if ($data && $data->getCarService()) {
                return $data->getCarService();
            } else if ($data && $data->getCarOwnerRequest() && $data->getCarOwnerRequest()->getCarService() ) {
                return $data->getCarOwnerRequest()->getCarService();
            } else {
                return $options['carService'];
            }
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em, $builder, $options) {
            $form = $event->getForm();
            $data = $event->getData();

            if (isset($data['post'])) {
                $form->add('post', 'entity', array(
                    'class' => 'AppBundle:CarServicePost',
                    'by_reference' => true,
                    'property' => 'name',
                    'required' => false,
                    'multiple' => false,
                    'choices' => $em->getRepository('AppBundle:CarServicePost')->findByCarService(getCarService($builder->getData(), $options))
                ));
            }

            if (isset($data['carOwnerRequest'])) {
                if (is_numeric($data['carOwnerRequest'])) {
                    $form->add('carOwnerRequest', 'entity', array(
                        'class' => 'AppBundle:CarOwnerRequest',
                        'by_reference' => true,
                        'property' => 'name',
                        'required' => false,
                        'multiple' => false,
                        'choices' => $em->getRepository('AppBundle:CarOwnerRequest')->findById($data['carOwnerRequest'])
                    ));
                } else if (is_array($data['carOwnerRequest'])) {
                    $form->add('carOwnerRequest', new CarOwnerRequestFormType(), array(
                            'em' => $em,
                            'type' => 'schedule_create_request',
                            'carService' => getCarService($builder->getData(), $options)));
                }
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($em, $options) {
            $form = $event->getForm();
            $data = $event->getData();

            $form->add('post', 'entity', array(
                    'class' => 'AppBundle:CarServicePost',
                    'by_reference' => true,
                    'property' => 'name',
                    'required' => false,
                    'multiple' => false,
                    'choices' => $em->getRepository('AppBundle:CarServicePost')->findByCarService(getCarService($data, $options))
            ));

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CarServiceSchedule',
            'cascade_validation' => false,
            'em' => null,
            'carOwnerRequest' => false,
            'carService' => null,
        ));
    }

    public function getName()
    {
        return 'car_service_schedule';
    }
}