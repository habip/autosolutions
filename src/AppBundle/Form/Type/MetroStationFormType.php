<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetroStationFormType extends AbstractType
{

    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;
    private $locality;

    public function __construct($em, $locality = false)
    {
        $this->em = $em;
        $this->locality = $locality;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
        ;

        if ($this->locality) {
            $builder->add('locality', 'entity', array(
                    'class' => 'AppBundle:Locality',
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

            if (isset($data['locality'])) {
                $locality = $data['locality'];

                $qb = $em->createQueryBuilder();
                $qb->select('l')->from('AppBundle:Locality', 'l')->where('l.id = :locality')->setParameter('locality', $locality);
                $q = $qb->getQuery();

                $form->add('locality', 'entity', array(
                        'class' => 'AppBundle:Locality',
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
                'data_class' => 'AppBundle\Entity\MetroStation',
                'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'metro_station';
    }
}