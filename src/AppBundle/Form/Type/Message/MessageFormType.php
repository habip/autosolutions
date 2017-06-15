<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class MessageFormType extends AbstractType
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('guid', 'text')
            ->add('body', 'textarea', array('label' => 'Message'))
            ->add('attachments', 'infinite_form_polycollection', array(
                    'types' => array(
                            new AttachmentFormType($this->em),
                            new ImageAttachmentFormType($this->em),
                    ),
                    'by_reference' => false,
                    'allow_add' => true
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Message\Message',
                'cascade_validation' => true,
                'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'message';
    }

}