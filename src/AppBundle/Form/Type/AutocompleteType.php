<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteType extends AbstractType
{

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();

        if (null !== $data) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $name = $accessor->getValue($data, $options['property']);
            $id = $accessor->getValue($data, 'id');
        } else {
            $name = null;
            $id = null;
        }
        $view->vars['value'] = $id;
        $view->vars['data_name'] = $name;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'choices' => array(),
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'autocomplete';
    }
}