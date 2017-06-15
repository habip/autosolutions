<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationInfoFormType extends AbstractType
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('legalForm', 'entity', array(
                'label' => 'organization_info.legal_form',
                'class' => 'AppBundle:LegalForm',
                'by_reference' => true,
                'property' => 'name',
                'empty_value' => 'organization_info.choose_legal_form',
                'em' => $this->em,
                'choices' => $this->em->getRepository('AppBundle:LegalForm')->findAll()
        ))
        ->add('fullName', 'text', array('label' => 'organization_info.full_name'))
        ->add('registrationNumber', 'text', array('label' => 'organization_info.registration_number'))
        ->add('inn', 'text', array('label' => 'organization_info.inn'))
        ->add('kpp', 'text', array('label' => 'organization_info.kpp'))
        ->add('legalAddress', 'text', array('label' => 'organization_info.legal_address'))
        ->add('bankAccountNumber', 'text', array('label' => 'organization_info.bank_account_number'))
        ->add('correspondentAccount', 'text', array('label' => 'organization_info.correspondent_account'))
        ->add('bankCode', 'text', array('label' => 'organization_info.bank_code'))
        ->add('bank', 'text', array('label' => 'organization_info.bank'))
        ->add('ceo', 'text', array('label' => 'organization_info.ceo'))
        ->add('chiefAccountant', 'text', array('label' => 'organization_info.chief_accountant'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\OrganizationInfo'
        ));
    }

    public function getName()
    {
        return 'organization_info';
    }
}