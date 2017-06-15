<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class ServiceValidator extends ConstraintValidator
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof Service) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Service');
        }

        if (null == $constraint->name) {
            throw new ConstraintDefinitionException(
                    'The Service constraint must have name option'
            );
        }

        $service = $this->container->get($constraint->name);

        if (null == $service) {
            throw new ConstraintDefinitionException(
                    sprintf('The Service constraint refers to non existing service %s', $constraint->name)
            );
        }

        if (!method_exists($service, $constraint->method)) {
            throw new ConstraintDefinitionException(sprintf('Method "%s" targeted by Service constraint does not exist in service %s', $constraint->method, $constaint->name));
        }

        $reflMethod = new \ReflectionMethod(get_class($service), $constraint->method);

        $reflMethod->invoke($service, $object, $this->context);

    }

}