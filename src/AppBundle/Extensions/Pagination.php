<?php

namespace AppBundle\Extensions;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Doctrine\Paginator;

class Pagination extends \Twig_Extension
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
            'pagination' => new \Twig_Function_Method($this, 'pagination', array('is_safe' => array('html'))),
        );
    }

    public function pagination(Paginator $paginator, $routeName, $routeParams = array(), $pageParamName = 'page', $range = 5)
    {
        if ($paginator->haveToPaginate()) {

            $hl = ceil($range / 2);
            $hr = $range - $hl;

            $numPages = ceil($paginator->count() / $paginator->getRecordsPerPage());

            $pages = array();

            if ($numPages <= $range + 2) {
                for ($i = 1; $i <= $numPages; $i++) {
                    array_push($pages, $i);
                }
            } else {
                if ($paginator->getPage() <= $hl) {
                    for ($i = 1; $i <= $range; $i++) {
                        array_push($pages, $i);
                    }
                    array_push($pages, $numPages);
                } else {
                    array_push($pages, 1);

                    if ($paginator->getPage() >= $numPages - $hr) {
                        for ($i = $numPages - $range + 1; $i <= $numPages ; $i++) {
                            array_push($pages, $i);
                        }
                    } else {
                        for ($i = $paginator->getPage() - $hl + 1; $i <= $paginator->getPage() + $hr ; $i++) {
                            array_push($pages, $i);
                        }

                        array_push($pages, $numPages);
                    }
                }
            }

            $rParams = array();

            if ($routeParams instanceof \IteratorAggregate) {
                $rParams = iterator_to_array($routeParams);
            } else if (is_array($routeParams)) {
                $rParams = $routeParams;
            }

            $params = array(
                    'pages' => $pages,
                    'route' => $routeName,
                    'routeParams' => $rParams,
                    'pageParam' => $pageParamName,
                    'currentPage' => $paginator->getPage());

            return $this->environment->render(':Pagination:slidingRange.html.twig', $params);
        }

        return '';
    }

    public function getName()
    {
        return 'pagination';
    }
}

