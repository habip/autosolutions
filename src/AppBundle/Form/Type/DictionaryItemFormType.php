<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryItemFormType extends AbstractType
{

    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;
    private $dictionary;

    public function __construct($em, $dictionary = false)
    {
        $this->em = $em;
        $this->dictionary = $dictionary;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
        ;

        if ($this->dictionary) {
            $builder->add('dictionary', 'entity', array(
                    'class' => 'AppBundle:Dictionary',
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

            if (isset($data['dictionary'])) {
                $dictionary = $data['dictionary'];

                $qb = $em->createQueryBuilder();
                $qb->select('d')->from('AppBundle:Dictionary', 'd')->where('d.id = :dictionary')->setParameter('dictionary', $dictionary);
                $q = $qb->getQuery();

                $form->add('dictionary', 'entity', array(
                        'class' => 'AppBundle:Dictionary',
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
                'data_class' => 'AppBundle\Entity\DictionaryItem',
                'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'dictionary_item';
    }
}