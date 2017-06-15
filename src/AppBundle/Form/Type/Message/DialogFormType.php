<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class DialogFormType extends AbstractType
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('relatedEntity')
            ->add('relatedEntityId')
            ->add('participants', 'collection', array(
                    'type' => new DialogParticipantFormType($this->em),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ))
            ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Message\Dialog',
                'cascade_validation' => true,
                'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'dialog';
    }

}
