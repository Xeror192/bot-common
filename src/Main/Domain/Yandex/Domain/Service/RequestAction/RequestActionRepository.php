<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service\RequestAction;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\Observer;
use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

class RequestActionRepository
{
    private ObjectRepository $objectRepository;
    private DoctrineRepository $doctrineRepository;

    public function __construct(DoctrineRepository $doctrineRepository)
    {
        $this->objectRepository = $doctrineRepository->getObjectRepository(RequestAction::class);
        $this->doctrineRepository = $doctrineRepository;
    }

    public function getQueryForSearch(string $query): QueryBuilder
    {
        return $this->doctrineRepository->getQueryBuilder()
            ->select('requestAction')->from(RequestAction::class, 'requestAction')
            ->join(Observer::class, 'observer')
            ->setParameter('query', $query);
    }
}