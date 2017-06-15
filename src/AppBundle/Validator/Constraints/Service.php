<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 *
 */
class Service extends Constraint
{
    public $message = 'Error';

    /**
     *
     * Name of validating service
     *
     * @var string
     */
    public $name;

    /**
     *
     * Name of method that will validate
     *
     * @var string
     */
    public $method = 'validate';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        // Invocation through annotations with an array parameter only
        if (is_array($options) && 1 === count($options) && isset($options['value'])) {
            $options = $options['value'];
        }

        parent::__construct($options);
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}