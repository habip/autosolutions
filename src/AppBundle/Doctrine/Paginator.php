<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Tools\Pagination as DoctrinePagination;
use AppBundle\Doctrine\Pagination as MyDoctrinePagination;

class Paginator implements \Countable, \IteratorAggregate
{

    private $paginator;
    private $page;
    private $recordsPerPage;

    private $iterator;
    private $result;

    /**
     *
     * Constructor
     *
     * @param Query|QueryBuilder $query
     * @param number $page
     * @param number $recordsPerPage
     * @param boolean $useBuildinPaginator Whether use build in Doctrine paginator or modified version which replaces all where conditions by one where in with ids list
     */
    public function __construct($query, $page=1, $recordsPerPage=10, $useBuildinPaginator = false, $useOutputWalkers = false)
    {
        $this->page = $page > 0 ? $page : 1;
        $this->recordsPerPage = $recordsPerPage;

        if ($useBuildinPaginator) {
            $this->paginator = new DoctrinePagination\Paginator($query);
        } else {
            $this->paginator = new MyDoctrinePagination\Paginator($query);
        }

        $this->paginator->setUseOutputWalkers($useOutputWalkers);

        $this->init();
    }

    private function init()
    {
        $this->paginator->getQuery()->setFirstResult(($this->page-1)*$this->recordsPerPage)->setMaxResults($this->recordsPerPage);

        $this->iterator = null;
        $this->result   = null;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->paginator->count();
    }

    /**
     * Current page number
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     *
     * @param integer $page
     * @return \AppBundle\Doctrine\Paginator
     */
    public function setPage($page)
    {
        $this->page = $page > 0 ? $page : 1;

        $this->init();

        return $this;
    }

    /**
     * Records per page
     *
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     *
     * @param integer $recordsPerPage
     * @return \AppBundle\Doctrine\Paginator
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
        $this->init();

        return $this;
    }

    public function getNumPages()
    {
        return ceil($this->count() / $this->recordsPerPage);
    }

    public function getLastPage()
    {
        return $this->getNumPages() > 0 ? $this->getNumPages() : 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if (null === $this->iterator) {
            $this->iterator = $this->paginator->getIterator();
        }
        return $this->iterator;
    }

    /**
     *
     * @return multitype:Ambigous <\Doctrine\ORM\Tools\Pagination\Paginator, \AppBundle\Doctrine\Pagination\Paginator>
     */
    public function getResult()
    {
        if (null === $this->result) {
            $this->result = array();

            foreach ($this->getIterator() as $record) {
                $this->result[] = $record;
            }
        }
        return $this->result;
    }

    /**
     *
     * @return boolean
     */
    public function haveToPaginate()
    {
        return $this->count() > $this->recordsPerPage;
    }
}