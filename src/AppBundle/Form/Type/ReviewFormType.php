<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array('choices' => array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5));

        $builder
            ->add('rating', 'choice', array('choices' => $choices))
            ->add('message', 'text')
            ->add('descriptionRating', 'choice', array('choices' => $choices, 'empty_data' => null))
            ->add('communicationRating', 'choice', array('choices' => $choices, 'empty_data' => null))
            ->add('priceRating', 'choice', array('choices' => $choices, 'empty_data' => null))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Review',
                'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'review';
    }
}