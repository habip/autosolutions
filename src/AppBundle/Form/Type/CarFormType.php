<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', 'autocomplete', array(
                    'class' => 'AppBundle:Brand',
                    'label' => 'Марка',
                    'by_reference' => true,
                    'property' => 'name',
                    'multiple' => false,
                    'choices' => array()
            ))
            ->add('model', 'autocomplete', array(
                    'class' => 'AppBundle:CarModel',
                    'label' => 'Модель',
                    'by_reference' => true,
                    'property' => 'name',
                    'multiple' => false,
                    'choices' => array()
            ))
            ->add('number', 'car_number', array('label' => 'Государственный номер'))
        ;

        if (!$options['basic_data_only']) {
            $builder
                ->add('year', 'number', array('label' => 'Год выпуска', 'required'=>false))
                ->add('mileage', 'number', array('label' => 'Пробег', 'required'=>false));
        }

        if ($options['images']) {
            $builder
                ->add('images', 'entity', array(
                        'class' => 'AppBundle:Image',
                        'by_reference' => true,
                        'property' => 'id',
                        'choices' => array(),
                        'multiple' => true
                ));
        }

        $em = $options['em'];

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['brand'])) {
                $brand = $data['brand'];

                $qb = $em->createQueryBuilder();
                $qb->select('b')->from('AppBundle:Brand', 'b')->where('b.id = :brand')->setParameter('brand', $brand);
                $q = $qb->getQuery();

                $form->add('brand', 'autocomplete', array(
                        'class' => 'AppBundle:Brand',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => false,
                        'choices' => $q->getResult()
                ));
            }

            if (isset($data['model'])) {
                $model = $data['model'];

                $qb = $em->createQueryBuilder();
                $qb->select('m')->from('AppBundle:CarModel', 'm')->where('m.id = :model')->setParameter('model', $model);
                $q = $qb->getQuery();

                $form->add('model', 'autocomplete', array(
                        'class' => 'AppBundle:CarModel',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => false,
                        'choices' => $q->getResult()
                ));
            }

            if (isset($data['images'])) {
                $images = $data['images'];
                $qb = $em->createQueryBuilder();
                $qb->select('i')
                    ->from('AppBundle:Image', 'i')
                    ->where($qb->expr()->in('i.id', $images));

                $q = $qb->getQuery();

                $form->add('images', 'entity', array(
                        'class' => 'AppBundle:Image',
                        'by_reference' => true,
                        'property' => 'id',
                        'choices' => $q->getResult(),
                        'multiple' => true
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Car',
                'csrf_protection' => false,
                'em' => null,
                'images' => false,
                'basic_data_only' => false
        ));
    }

    public function getName()
    {
        return 'car';
    }


}