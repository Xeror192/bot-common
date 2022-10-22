<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * RequestAction
 *
 * @ORM\Table(name="bot_yandex_request_actions")
 * @ORM\Entity
 */
class RequestAction
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(name="uuid", type="uuid", unique=true)
     */
    private UuidInterface $uuid;

    /**
     * @var UuidInterface
     * @ORM\Column(name="observer_uuid", type="uuid", nullable=false)
     */
    private UuidInterface $observerUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="string", length=255, nullable=false)
     */
    private string $query;

    /**
     * @var array
     *
     * @ORM\Column(name="arguments", type="json", nullable=false)
     */
    private array $arguments;

    public function __construct(string $query, UuidInterface $observerUuid, array $arguments = [])
    {
        $this->uuid = Uuid::uuid4();
        $this->query = $query;
        $this->observerUuid = $observerUuid;
        $this->arguments = $arguments;
    }

    /**
     * @return UuidInterface
     */
    public function getObserverUuid(): UuidInterface
    {
        return $this->observerUuid;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

}