<?php

namespace Jefero\Bot\Main\Domain\Common\Service;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Doctrine\Persistence\ObjectRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerRepository
{
    private ObjectRepository $objectRepository;
    private DoctrineRepository $doctrineRepository;

    public function __construct(DoctrineRepository $doctrineRepository)
    {
        $this->objectRepository = $doctrineRepository->getObjectRepository(Customer::class);
        $this->doctrineRepository = $doctrineRepository;
    }

    public function findOneByUuid(UuidInterface $uuid): ?Customer
    {
        return $this->objectRepository->findOneBy([
            'uuid' => $uuid
        ]);
    }

    public function findByUsername(string $username): ?Customer
    {
        return $this->objectRepository->findOneBy([
            'username' => $username
        ]);
    }

    public function getByUsername(string $username): Customer
    {
        $customer = $this->objectRepository->findOneBy([
            'username' => $username
        ]);

        if (!$customer) {
            throw new NotFoundCustomerException();
        }

        return $customer;
    }
}