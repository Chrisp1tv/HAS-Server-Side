<?php

namespace App\Util;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Paginator
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Paginator
{
    /**
     * @var DoctrinePaginator
     */
    private $doctrinePaginator;

    /**
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $numberOfPages;

    /**
     * @param Query $query
     * @param       $itemsPerPage
     * @param       $page
     */
    public function __construct(Query $query, $itemsPerPage, $page)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->page = $page ?? 1;
        $this->setUpDoctrinePaginator($query);
    }

    /**
     * @return bool
     */
    public function currentPageIsInvalid()
    {
        return !$this->isEmpty() and (!ctype_digit(strval($this->getPage())) or $this->getNumberOfPages() < $this->getPage() or $this->getPage() < 1);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 === $this->countResults();
    }

    /**
     * @return int
     */
    public function countResults()
    {
        return $this->doctrinePaginator->count();
    }

    /**
     * @param Query $query
     */
    private function setUpDoctrinePaginator(Query $query)
    {
        $query
            ->setFirstResult($this->itemsPerPage * ($this->page - 1))
            ->setMaxResults($this->itemsPerPage);

        $this->doctrinePaginator = new DoctrinePaginator($query);
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        if (null == $this->numberOfPages) {
            $this->numberOfPages = ceil($this->countResults() / $this->itemsPerPage);
        }

        return (int)$this->numberOfPages;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getResults()
    {
        return $this->doctrinePaginator->getIterator();
    }
}
