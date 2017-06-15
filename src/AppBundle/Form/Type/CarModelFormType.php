<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarModelFormType extends AbstractType
{

    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;
    private $brand;

    public function __construct($em, $brand = false)
    {
        $this->em = $em;
        $this->brand = $brand;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('vehicleType', 'entity', array(
                    'class' => 'AppBundle:VehicleType',
                    'by_reference' => true,
                    'property' => 'name'
            ))
            ->add('vehicleClass', 'text')
        ;

        if ($this->brand) {
            $builder->add('brand', 'entity', array(
                    'class' => 'AppBundle:Brand',
                    'by_reference' => true,
                    'property' => 'name',
                    'multiple' => false,
                    'choices' => array()
            ));
        }

        $em = $this->em;

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

                $form->add('brand', 'entity', array(
                        'class' => 'AppBundle:Brand',
                        'by_reference' => true,
                        'property' => 'name',
                        'multiple' => false,
                        'choices' => $q->getResult()
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\CarModel',
                'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'car_model';
    }
}