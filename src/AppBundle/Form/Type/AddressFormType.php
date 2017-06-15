<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressFormType extends AbstractType
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('streetAddress', 'text', array('label' => 'address.street_address'))
            ->add('locality', 'autocomplete', array(
                        'label' => 'address.locality',
                        'class' => 'AppBundle:Locality',
                        'empty_value' => 'address.choose_locality',
                        'property' => 'name',
                        'choices' => array(),
                        'required' => true
                ))
        ;

        $em = $this->em;

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

            $r = $em->createQuery('select c from AppBundle:Country c order by c.name')
                        ->getResult(Query::HYDRATE_ARRAY);

            $countries = array();

            foreach ($r as $row) {
                $countries[$row['id']] = $row['name'];
            }

            $form->add('country', 'choice', array(
                    'label' => 'address.country',
                    'mapped' => false,
                    'empty_value' => 'address.choose_country',
                    'choices' => $countries,
                    'required' => true,
                    'data' => $data->getLocality() ? $data->getLocality()->getCountry()->getId() : null
            ));

            $form->add('locality', 'autocomplete', array(
                    'label' => 'address.locality',
                    'class' => 'AppBundle:Locality',
                    'empty_value' => 'address.choose_locality',
                    'property' => 'localityName',
                    'choices' => $data->getLocality() ? array($data->getLocality()) : array(),
                    'required' => false
            ));

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Address',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'address';
    }
}