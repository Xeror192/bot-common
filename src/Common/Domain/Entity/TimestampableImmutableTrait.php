<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\Domain\Entity;

use DateTimeImmutable;

/**
 * Trait TimestampableImmutableTrait
 * @package App\Common\Domain\Entity
 */
trait TimestampableImmutableTrait
{
    /**
     * @var DateTimeImmutable
     * @Doctrine\ORM\Mapping\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     * @Doctrine\ORM\Mapping\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $updatedAt;


    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
