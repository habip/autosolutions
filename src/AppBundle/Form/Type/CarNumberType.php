<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class CarNumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $vars = array('number_part' => '', 'region_part' => '');
        if (preg_match('/^([^0-9][0-9]{3}[^0-9]{2})([0-9]{1,3})$/u', $view->vars['value'], $matches)) {
            $vars = array('number_part' => $matches[1], 'region_part' => $matches[2]);
        }
        
        $view->vars = array_merge($view->vars, $vars);
    }
    
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'car_number';
    }

}