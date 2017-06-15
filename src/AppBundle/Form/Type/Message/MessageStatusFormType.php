<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;
use AppBundle\Entity\Message\MessageStatus;

class MessageStatusFormType extends AbstractType
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                    'choices' => array(
                        MessageStatus::STATUS_DELIVERED => MessageStatus::STATUS_DELIVERED,
                        MessageStatus::STATUS_READ => MessageStatus::STATUS_READ
                    )
            ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            $ms = $form->getData();
            if ($ms->getStatus() != MessageStatus::STATUS_NEW) {
                $statuses = array();
                switch ($ms->getStatus()) {
                    case MessageStatus::STATUS_DELIVERED:
                        $statuses[MessageStatus::STATUS_READ] = MessageStatus::STATUS_READ;
                        break;
                    case MessageStatus::STATUS_READ:
                        break;
                }
                $form->add('status', 'choice', array('choices' => $statuses));
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Message\MessageStatus',
                'cascade_validation' => true,
                'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'messageStatus';
    }

}