<?php

namespace Jefero\Bot\Common\Infrastructure\Helper\Pagination;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

class Paginator
{
    private Query $query;

    private int $pageSize;

    private int $page;

    private int $pageCount;

    /** @var array|ArrayCollection */
    private $results;

    public function __construct(Query $query, int $page = 1, int $pageSize = 20)
    {
        $this->query = $query;
        $this->pageSize = $pageSize;
        $this->page = $page;
    }

    public function make(int $mode = AbstractQuery::HYDRATE_OBJECT): void
    {
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($this->query);
        $totalItems = count($paginator);
        $this->pageCount = (int)ceil($totalItems / $this->pageSize);

        $paginator->getQuery()
            ->setFirstResult($this->pageSize * ($this->page - 1)) // set the offset
            ->setMaxResults($this->pageSize);

        $this->results = $paginator->getQuery()->getResult($mode);
    }

    /**
     * @return array|ArrayCollection
     */
    public function getResults()
    {
        return $this->results;
    }

    public function getPrevPages(): array
    {
        if ($this->page == 1) {
            return [];
        }

        $result = [];
        $page = $this->page;
        $pages = 0;

        while ($page != 1 && $pages != 3) {
            $pages++;
            $page--;
            $result[] = $page;
        }
        asort($result);
        return $result;
    }

    public function getNextPages(): array
    {
        if ($this->page == $this->pageCount) {
            return [];
        }

        $result = [];
        $page = $this->page;
        $pages = 0;

        while ($page != $this->pageCount && $pages != 3) {
            $pages++;
            $page++;
            $result[] = $page;
        }

        return $result;
    }

    public function getLastPage(): int
    {
        return $this->pageCount;
    }

    public function next(): int
    {
        if ($this->page == $this->pageCount) {
            return 0;
        }

        return $this->page + 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->pageCount;
    }

    public function hasLast(): bool
    {
        if ($this->page == $this->pageCount) {
            return false;
        }

        if (!in_array($this->getLastPage(), $this->getNextPages())) {
            return true;
        }

        return false;
    }

    public function isEmpty(): bool
    {
        return !(bool)count($this->results);
    }
}
