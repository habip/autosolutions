<?php
namespace AppBundle\Extensions;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TextUtils extends \Twig_Extension
{
    private $container;
    private $environment;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    public function getFunctions()
    {
        return array(
            'period_to_str' => new \Twig_Function_Method($this, 'periodToStr', array('is_safe' => array('html'))),
        );
    }
    public function periodToStr($period)
    {
        switch ($period) {
            case 'P1M':
                return '1 месяц';
            case 'P3M':
                return '3 месяца';
            case 'P6M':
                return 'полгода';
            case 'P1Y':
                return '1 год';
        }
    }

    public function getName()
    {
        return 'text_utils';
    }
}