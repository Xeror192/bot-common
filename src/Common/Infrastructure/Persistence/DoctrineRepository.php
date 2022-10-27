<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Security;

class DoctrineRepository
{
    protected EntityManagerInterface $em;

    protected Security $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->em = $entityManager;
        $this->security = $security;
    }

    /**
     * @param string $className
     * @psalm-param class-string $className
     * @psalm-return ObjectRepository
     */
    public function getObjectRepository(string $className): ObjectRepository
    {
        return $this->em->getRepository($className);
    }

    public function createQuery(string $sql): Query
    {
        return $this->em->createQuery($sql);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder();
    }

    /**
     * @param mixed $entity
     */
    public function persist($entity): void
    {
        $this->em->persist($entity);
    }

    /**
     * @param mixed $entity
     */
    public function remove($entity): void
    {
        $this->em->remove($entity);
    }

    public function flush(): void
    {
        $this->em->flush();
    }

    /**
     * @param object ...$entities
     */
    public function save(object ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param object ...$entities
     */
    public function delete(object ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->em->remove($entity);
        }
        $this->em->flush();
    }
}
