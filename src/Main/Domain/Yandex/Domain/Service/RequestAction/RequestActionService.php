<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service\RequestAction;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Doctrine\ORM\QueryBuilder;

class RequestActionService
{
    private RequestActionRepository $repository;

    public function __construct(RequestActionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getQueryForSearch(string $query): QueryBuilder
    {
        return $this->repository->getQueryForSearch($query);
    }
}