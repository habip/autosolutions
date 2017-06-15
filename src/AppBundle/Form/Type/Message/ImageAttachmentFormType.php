<?php

namespace AppBundle\Form\Type\Message;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\Query;

class ImageAttachmentFormType extends AttachmentFormType
{

    protected $dataClass = 'AppBundle\Entity\Message\ImageAttachment';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('photo', 'entity', array(
                'class' => 'AppBundle:Image',
                'by_reference' => false,
                'property' => 'fileUrl',
                'choices' => array()
        ));

        $em = $this->em;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($em) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (isset($data['photo'])) {
                $photo = $em->getRepository('AppBundle:Image')->findOneByPkPhoto($data['image']);

                $form->add('photo', 'entity', array(
                    'class' => 'AppBundle:Image',
                    'by_reference' => false,
                    'property' => 'fileUrl',
                    'choices' => $photo == null ? array() : array($photo)
                ));
            }
        });
    }

    public function getName()
    {
        return 'image_attachment';
    }

}