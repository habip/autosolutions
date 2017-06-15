<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class AttachmentFormType extends AbstractType
{

    protected $em;
    protected $dataClass = 'AppBundle\Entity\Message\Attachment';

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_type', 'hidden', array(
            'data'   => $this->getName(),
            'mapped' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => $this->dataClass,
                'cascade_validation' => true,
                'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'attachment';
    }

}