<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use AppBundle\Entity\CarOwnerRequest;

class CarOwnerRequestScheduleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!in_array($options['type'], array('schedule_edit_request', 'schedule_create_request'))) {
            throw new InvalidArgumentException('wrong value of type option');
        }

        $builder
            ->add('services', 'entity', array(
                'class' => 'AppBundle:Service',
                'by_reference' => true,
                'property' => 'name',
                'required' => false,
                'multiple' => true,
                'choices' => array(),
            ))
            ->add('schedule', new CarServiceScheduleFormType(), array(
                'em' => $options['em'],
                'carService' => $builder->getData() && $builder->getData()->getCarService() ? $builder->getData()->getCarService() : $options['carService']
            ))
            ->add('status', 'choice', array(
                'choices' => CarOwnerRequest::$statusNames
            ))
        ;

        if ('schedule_create_request' == $options['type']) {
            $builder
                ->add('phone', 'text')
                ->add('car', new CarFormType(), array('em' => $options['em'], 'basic_data_only' => true))
                ->add('carOwner', new CarOwnerFormType(), array(
                        'em' => $options['em'],
                        'embed_parent' => 'CarServiceSchedule',
                        'cascade_validation' => true
                ))
            ;
        } else if ('schedule_edit_request' == $options['type']) {
            $builder->add('items', 'collection', array(
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'type' => new CarOwnerRequestItemFormType(),
            ));
        }

        $em = $options['em'];

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em, $builder, $options) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
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
            'type' => 'schedule_edit_request',
        ));
    }

    public function getName()
    {
        return '';
    }
}