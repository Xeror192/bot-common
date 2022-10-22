<?php

namespace Jefero\Bot\Main\Domain\Common\Entity;

use Jefero\Bot\Common\Domain\ValueObject\Phone;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bot_customers")
 */
class Customer
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(name="uuid", type="uuid", unique=true)
     */
    private UuidInterface $uuid;

    /**
     * @ORM\Embedded(class="App\Common\Domain\ValueObject\Phone", columnPrefix=false)
     */
    private Phone $phone;

    /**
     * @var string
     * @ORM\Column(name="username", type="string")
     */
    private string $username;

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private ?string $name;

    public function __construct(string $username, Phone $phone)
    {
        $this->uuid = Uuid::uuid4();
        $this->phone = $phone;
        $this->username = $username;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Phone $phone
     */
    public function setPhone(Phone $phone): void
    {
        $this->phone = $phone;
    }
}