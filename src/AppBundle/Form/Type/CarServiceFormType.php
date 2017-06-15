<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;
use Symfony\Component\Form\FormInterface;

class CarServiceFormType extends AbstractType
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['em'];

        $builder
            ->add('name', 'text', array('label' => 'car_service.name'))
            ->add('locality', 'autocomplete', array(
                    'label' => 'address.locality',
                    'class' => 'AppBundle:Locality',
                    'property' => 'name',
                    'empty_value' => 'address.choose_locality',
                    'required' => true
            ))
            ->add('district', 'autocomplete', array(
                    'label' => 'Район',
                    'class' => 'AppBundle:District',
                    'property' => 'name',
                    'empty_value' => 'Выберите район',
                    'required' => false
            ))
            ->add('station', 'autocomplete', array(
                    'label' => 'Метро',
                    'class' => 'AppBundle:MetroStation',
                    'property' => 'name',
                    'empty_value' => 'Выберите метро',
                    'required' => false
            ))
            ->add('highway', 'autocomplete', array(
                    'label' => 'Магистраль',
                    'class' => 'AppBundle:Highway',
                    'property' => 'name',
                    'empty_value' => 'Выберите магистраль',
                    'required' => false
            ))
            ->add('streetAddress', 'text', array('label' => 'Адрес', 'required' => false))
            ->add('phone', 'text', array('label' => 'Phone', 'required' => false))
            ->add('email', 'text', array('label' => 'Email', 'required' => false))
            ->add('director', 'text', array('label' => 'car_service.director', 'required' => false))
            ->add('description', 'textarea', array('label' => 'Описание', 'required' => false))

            ->add('is24Hrs', 'checkbox', array('required' => false))

            ->add('monStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('monEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isMonDayOff', 'checkbox', array('required' => false))

            ->add('tueStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('tueEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isTueDayOff', 'checkbox', array('required' => false))

            ->add('wedStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('wedEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isWedDayOff', 'checkbox', array('required' => false))

            ->add('thuStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('thuEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isThuDayOff', 'checkbox', array('required' => false))

            ->add('friStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('friEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isFriDayOff', 'checkbox', array('required' => false))

            ->add('satStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('satEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isSatDayOff', 'checkbox', array('required' => false))

            ->add('sunStart', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('sunEnd', 'time', array('widget' => 'single_text', 'required' => false))
            ->add('isSunDayOff', 'checkbox', array('required' => false))

            ->add('latitude', 'text', array('label' => 'Широта', 'required' => false))
            ->add('longitude', 'text', array('label' => 'Долгота', 'required' => false))
            ->add('isOfficial', 'checkbox', array('required' => false))
        ;

        if (in_array('isBlocked', $options['additional_fields'])) {
            $builder->add('isBlocked', 'checkbox');
        }
        if (in_array('services', $options['additional_fields'])) {
            $builder->add('services', 'entity', array(
             'class' => 'AppBundle:Service',
             'by_reference' => false,
             'property' => 'name',
             'choices' => array(),
             'multiple' => true
            ));
        }
        if (in_array('servedCarBrands', $options['additional_fields'])) {
            $builder->add('servedCarBrands', 'entity', array(
                    'class' => 'AppBundle:Brand',
                    'by_reference' => false,
                    'property' => 'name',
                    'choices' => array(),
                    'multiple' => true
            ));
        }

        if (in_array('paymentTypes', $options['additional_fields'])) {
            $builder->add('paymentTypes', 'entity', array(
                    'class' => 'AppBundle:PaymentType',
                    'by_reference' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'expanded' => true
            ));
        }

        if (in_array('sereviceTypes', $options['additional_fields'])) {
            $builder->add('serviceTypes', 'collection', array(
                    'type' => new ServiceTypeFormType(),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
            ));
        }

        $imageProperties = array(
                'class' => 'AppBundle:Image',
                'property' => 'id',
                'by_reference' => true,
                'choices' => array(),
        );

        if (in_array('image', $options['additional_fields'])) {
            $builder->add('image', 'entity', $imageProperties);
        }

        if (in_array('images', $options['additional_fields'])) {
            if (!in_array('image', $options['additional_fields'])) {
                $builder->add('image', 'entity', $imageProperties);
            }

            $builder
                ->add('logo', 'entity', $imageProperties)
                ->add('inspectorZoneImage', 'entity', $imageProperties)
                ->add('clientZoneImage', 'entity', $imageProperties)
                ->add('washingZoneImage', 'entity', $imageProperties)
                ->add('tireServiceZoneImage', 'entity', $imageProperties)
                ->add('benchRepairZoneImage', 'entity', $imageProperties)
                ->add('bodyRepairZoneImage', 'entity', $imageProperties)
                ->add('employeesImage', 'entity', $imageProperties)
            ;
        }

        if (in_array('posts', $options['additional_fields'])) {
            $builder->add('posts', 'collection', array(
                    'type' => new CarServicePostFormType(),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
            ));
        }

        if (in_array('questionnaire', $options['additional_fields'])) {
            $builder
                ->add('site', 'text', array('label' => 'Сайт', 'required' => false))
                ->add('clientsCount', 'text', array('label' => 'Среднее количество клиентов', 'required' => false))
                ->add('isStorehouseAvailable', 'checkbox', array('label' => 'Наличие склада запчастей', 'required' => false))
                ->add('isDetailOrderAvailable', 'checkbox', array('label' => 'Заказ запчастей', 'required' => false))
                ->add('isInspectorAvailable', 'checkbox', array('label' => 'Наличие приемки', 'required' => false))
                ->add('guestParkingCount', 'text', array('label' => 'Количество машиномест на гостевой стоянке', 'required' => false))
                ->add('waitingPlacesCount', 'text', array('label' => 'Количество мест ожидания', 'required' => false))
                ->add('isComfortableWaitingPlacesAvailable', 'checkbox', array('label' => 'Оборудованность места ожидания мягкой мебелью', 'required' => false))
                ->add('isTVAvailable', 'checkbox', array('label' => 'Телевидение', 'required' => false))
                ->add('isFreeWIFIAvailable', 'checkbox', array('label' => 'Бесплатный WiFi', 'required' => false))
                ->add('isColdDrinkAvailable', 'checkbox', array('label' => 'Предоставление холодных напитков', 'required' => false))
                ->add('isHotDrinkAvailable', 'checkbox', array('label' => 'Предоставление горячих напитков', 'required' => false))
                ->add('isAccessToRepairZoneAvailable', 'checkbox', array('label' => 'Допуск в ремзону', 'required' => false))
                ->add('isVisualAccessToRepairZoneAvailable', 'checkbox', array('label' => 'Визуально вне ремзоны', 'required' => false))
                ->add('isVideoAccessToRepairZoneAvailable', 'checkbox', array('label' => 'Видеонаблюдение', 'required' => false))
                ->add('isFreeTransportServiceAvailable', 'checkbox', array('label' => 'Бесплатная транспортная услуга по доставке до дому/работы', 'required' => false))
                ->add('isReplacementCarServiceAvailable', 'checkbox', array('label' => 'Предоставление подменного автомобиля', 'required' => false))
                ->add('additional', 'textarea', array('label' => 'Дополнительно', 'required' => false))

            ;
        }

        $em = $this->em;
        $formType = $this;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($em, $formType, $options) {
            /* @var $data \AppBundle\Entity\CarService */
            $form = $event->getForm();
            $data = $event->getData();
            $props = array(
                'class' => 'AppBundle:Image',
                'property' => 'id',
                'by_reference' => true,
            );

            if (in_array('image', $options['additional_fields']) || in_array('images', $options['additional_fields'])) {
                if ($data->getImage()) $form->add('image', 'entity', array_merge($props, array('choices' => array($data->getImage()))));
            }

            if (in_array('images', $options['additional_fields'])) {
                if ($data->getLogo()) $form->add('logo', 'entity', array_merge($props, array('choices' => array($data->getLogo()))));
                if ($data->getInspectorZoneImage()) $form->add('inspectorZoneImage', 'entity', array_merge($props, array('choices' => array($data->getInspectorZoneImage()))));
                if ($data->getClientZoneImage()) $form->add('clientZoneImage', 'entity', array_merge($props, array('choices' => array($data->getClientZoneImage()))));
                if ($data->getWashingZoneImage()) $form->add('washingZoneImage', 'entity', array_merge($props, array('choices' => array($data->getWashingZoneImage()))));
                if ($data->getTireServiceZoneImage()) $form->add('tireServiceZoneImage', 'entity', array_merge($props, array('choices' => array($data->getTireServiceZoneImage()))));
                if ($data->getBenchRepairZoneImage()) $form->add('benchRepairZoneImage', 'entity', array_merge($props, array('choices' => array($data->getBenchRepairZoneImage()))));
                if ($data->getBodyRepairZoneImage()) $form->add('bodyRepairZoneImage', 'entity', array_merge($props, array('choices' => array($data->getBodyRepairZoneImage()))));
                if ($data->getEmployeesImage()) $form->add('employeesImage', 'entity', array_merge($props, array('choices' => array($data->getEmployeesImage()))));
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em, $formType) {
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

            if (isset($data['district']) && $data['district']) {
                $q = $em->createQuery('select d from AppBundle:District d where d.id = :district')
                    ->setParameter('district', $data['district']);

                $form->add('district', 'autocomplete', array(
                        'label' => 'Район',
                        'class' => 'AppBundle:District',
                        'property' => 'name',
                        'empty_value' => 'Выберите район',
                        'choices' => $q->getResult(),
                        'required' => false
                ));
            }

            if (isset($data['station']) && $data['station']) {
                $q = $em->createQuery('select m from AppBundle:MetroStation m where m.id = :station')
                    ->setParameter('station', $data['station']);

                $form->add('station', 'autocomplete', array(
                        'label' => 'Метро',
                        'class' => 'AppBundle:MetroStation',
                        'property' => 'name',
                        'empty_value' => 'Выберите метро',
                        'choices' => $q->getResult(),
                        'required' => false
                ));
            }

            if (isset($data['highway']) && $data['highway']) {
                $q = $em->createQuery('select h from AppBundle:Highway h where h.id = :highway')
                    ->setParameter('highway', $data['highway']);

                $form->add('highway', 'autocomplete', array(
                        'label' => 'Магистраль',
                        'class' => 'AppBundle:Highway',
                        'property' => 'name',
                        'empty_value' => 'Выберите магистраль',
                        'choices' => $q->getResult(),
                        'required' => false
                ));
            }

            if (isset($data['district']) && $data['district']) {
                $q = $em->createQuery('select l from AppBundle:District l where l.id = :district')
                ->setParameter('district', $data['district']);

                $form->add('district', 'autocomplete', array(
                        'label' => 'Район',
                        'class' => 'AppBundle:District',
                        'property' => 'name',
                        'empty_value' => 'Выберите район',
                        'choices' => $q->getResult(),
                        'required' => false
                ));
            }

            if (isset($data['services'])) {
                $services = $data['services'];

                $qb = $em->createQueryBuilder();
                $qb->select('s')->from('AppBundle:Service', 's')->where($qb->expr()->in('s.id', $services));
                $q = $qb->getQuery();

                $form->add('services', 'entity', array(
                    'class' => 'AppBundle:Service',
                    'by_reference' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'choices' => $q->getResult()
                ));
            }

            if (isset($data['servedCarBrands'])) {
                $servedCarBrands = $data['servedCarBrands'];

                $qb = $em->createQueryBuilder();
                $qb->select('b')->from('AppBundle:Brand', 'b')->where($qb->expr()->in('b.id', $servedCarBrands));
                $q = $qb->getQuery();

                $form->add('servedCarBrands', 'entity', array(
                    'class' => 'AppBundle:Brand',
                    'by_reference' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'choices' => $q->getResult()
                ));
            }

            $formType->handleImages(array(
                    'logo',
                    'image',
                    'inspectorZoneImage',
                    'clientZoneImage',
                    'washingZoneImage',
                    'tireServiceZoneImage',
                    'benchRepairZoneImage',
                    'bodyRepairZoneImage',
                    'employeesImage'
            ), $data, $form, $em);

        });

    }

    public function handleImages(array $names, &$data, FormInterface $form, EntityManager $em)
    {
        foreach ($names as $name) {
            $this->handleImage($name, $data, $form, $em);
        }
    }

    public function handleImage($name, &$data, FormInterface $form, EntityManager $em)
    {
        if (isset($data[$name])) {
            $image = $data[$name];

            $form->add($name, 'entity', array(
                    'class' => 'AppBundle:Image',
                    'by_reference' => true,
                    'property' => 'id',
                    'choices' => $em->getRepository('AppBundle:Image')->findById($image)
            ));
        }

        return $this;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\CarService',
                'additional_fields' => [],
                'em' => null,
        ));
    }

    public function getName()
    {
        return 'car_service';
    }

}