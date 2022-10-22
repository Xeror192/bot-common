<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\Emotion;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Doctrine\Persistence\ObjectRepository;

class EmotionRepository
{
    private ObjectRepository $objectRepository;
    private DoctrineRepository $doctrineRepository;

    public function __construct(DoctrineRepository $doctrineRepository)
    {
        $this->objectRepository = $doctrineRepository->getObjectRepository(Emotion::class);
        $this->doctrineRepository = $doctrineRepository;
    }

    public function findByCode(string $code)
    {
        return $this->objectRepository->findOneBy([
            'code' => $code
        ]);
    }

    public function findByName(string $name)
    {
        return $this->objectRepository->findOneBy([
            'name' => $name
        ]);
    }
}