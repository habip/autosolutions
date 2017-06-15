<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeFormType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => 'Имя'))
            ->add('lastName', 'text', array('label' => 'Фамилия'))
            ->add('position', 'text', array('label' => 'Должность'))
            ->add('carService', 'entity', array(
                'label' => 'Автосервис',
                'class' => 'AppBundle:CarService',
                'by_reference' => true,
                'property' => 'name',
                'required' => true,
                'multiple' => false,
                'choices' => $options['em']->getRepository('AppBundle:CarService')->findByCompany($options['company'])
            ))
            ->add('user', new UserRegistrationFormType(), array('email' => false, 'username' => true, 'validation_groups' => array('Default', 'password', 'unique_username')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Employee',
                'cascade_validation' => true,
                'em' => null,
                'company' => null
        ));
    }

    public function getName()
    {
        return 'employee';
    }

}
