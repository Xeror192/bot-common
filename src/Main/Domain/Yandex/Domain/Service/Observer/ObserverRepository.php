<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service\Observer;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\Observer;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Doctrine\Persistence\ObjectRepository;
use Ramsey\Uuid\UuidInterface;

class ObserverRepository
{
    private ObjectRepository $objectRepository;
    private DoctrineRepository $doctrineRepository;

    public function __construct(DoctrineRepository $doctrineRepository)
    {
        $this->objectRepository = $doctrineRepository->getObjectRepository(Observer::class);
        $this->doctrineRepository = $doctrineRepository;
    }

    public function getOneByUuid(UuidInterface $uuid)
    {
        return $this->objectRepository->findOneBy([
            'uuid' => $uuid
        ]);
    }
}