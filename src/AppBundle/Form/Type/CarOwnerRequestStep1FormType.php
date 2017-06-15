<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class CarOwnerRequestStep1FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('carOwnerDate', 'date', array(
                'label' => 'Выберите удобную дату заезда',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy'
            ))
            ->add('carOwnerTimePeriod', 'choice', array(
                'label' => 'Выберите удобное для вас время',
                'choices' => array(
                    1 => 'Первая половина дня',
                    2 => 'Вторая половина дня'
                )
            ))
            ->add('car', new CarFormType(), array('em' => $options['em']))
            ->add('services', 'entity', array(
                    'class' => 'AppBundle:Service',
                    'by_reference' => true,
                    'property' => 'name',
                    'required' => false,
                    'multiple' => true
            ));

        $em = $options['em'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            /* @var $carOwnerRequest \AppBundle\Entity\CarOwnerRequest */
            $carOwnerRequest = $event->getData();

            if ($carOwnerRequest->getCarService()->isSchedulable()) {
                $form
                ->add('carOwnerDate', 'datetime', array(
                        'widget' => 'single_text',
                        'required' => false
                ))
                ->remove('carOwnerTimePeriod');
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['services'])) {
                $services = $data['services'];

                $qb = $em->createQueryBuilder();
                $qb->select('s')->from('AppBundle:Service', 's')->where($qb->expr()->in('s.id', $services));
                $q = $qb->getQuery();

                $form->add('services', 'entity', array(
                        'class' => 'AppBundle:Service',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => true,
                        'required' => false,
                        'choices' => $q->getResult()
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CarOwnerRequest',
            'cascade_validation' => true,
            'em' => null
        ));
    }

    public function getName()
    {
        return 'car_owner_request';
    }
}