<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class CarOwnerRequestStep2FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('carOwner', new CarOwnerFormType(), array(
                'em' => $options['em'],
                'embed_parent' => 'CarOwnerRequest',
                'cascade_validation' => true
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'Дополнительная информация'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CarOwnerRequest',
            'cascade_validation' => true,
            'em' => null
        ));
    }

    public function getName()
    {
        return 'car_owner_request';
    }
}