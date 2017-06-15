<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class DialogParticipantFormType extends AbstractType
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                    'class' => 'AppBundle:User',
                    'by_reference' => false,
                    'property' => 'id',
                    'choices' => array()
            ))
            ;

        $em = $this->em;
        $factory = $builder->getFormFactory();

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($factory, $em) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['user'])) {
                $user = $em->getRepository('AppBundle:User')->findOneById($data['user']);

                $form->add('user', 'entity', array(
                        'class' => 'AppBundle:User',
                        'by_reference' => false,
                        'property' => 'id',
                        'choices' => $user == null ? array() : array($user)
                ));
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Message\DialogParticipant',
                'cascade_validation' => true,
                'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'dialog_participant';
    }

}
