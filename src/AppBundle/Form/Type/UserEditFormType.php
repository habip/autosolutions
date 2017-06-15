<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserEditFormType extends AbstractType
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', 'entity', array(
                    'class' => 'AppBundle:Image',
                    'by_reference' => true,
                    'property' => 'id',
                    'choices' => array(),
            ));

        $em = $this->em;
        $factory = $builder->getFormFactory();
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($factory, $em) {
            $form = $event->getForm();
            $data = $event->getData();
            if (null === $data) {
                return;
            }
            if (isset($data['image'])) {
                $image = $data['image'];
                $qb = $em->createQueryBuilder();
                $qb->select('i')
                    ->from('AppBundle:Image', 'i')
                    ->where($qb->expr()->eq('i.id', $image));
                $q = $qb->getQuery();

                $form->add('image', 'entity', array(
                        'class' => 'AppBundle:Image',
                        'by_reference' => true,
                        'property' => 'id',
                        'choices' => $q->getResult()
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}