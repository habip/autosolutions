<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkplaceFormType extends AbstractType
{
    private $edit;

    public function __construct($edit = false)
    {
        $this->edit = $edit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attr = array();
        if ($this->edit) {
            $attr['attr'] = array('placeholder' => 'Введите значение токена если вы хотите его заменить');
            $attr['required'] = false;
        }

        $builder
            ->add('plainToken', 'text', array_merge(array('label' => 'company.workplace.token'), $attr))
            ->add('description', 'textarea', array('label' => 'company.workplace.description'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Workplace'
        ));
    }

    public function getName()
    {
        return 'workplace';
    }

}
