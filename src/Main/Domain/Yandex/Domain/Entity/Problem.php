<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bot_yandex_problems")
 */
class Problem
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name = '';

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private int $score = 0;

    /**
     * @var array
     *
     * @ORM\Column(name="keywords", type="json", nullable=false)
     */
    private array $keywords;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }
}