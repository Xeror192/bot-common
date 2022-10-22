<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bot_yandex_observers")
 */
class Observer
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(name="uuid", type="uuid", unique=true)
     */
    private UuidInterface $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, nullable=false)
     */
    private string $code = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private bool $enabled = false;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}