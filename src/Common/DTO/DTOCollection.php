<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\DTO;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends ArrayCollection<TKey, T>
 * @psalm-consistent-constructor
 */
class DTOCollection extends ArrayCollection implements \JsonSerializable
{
    protected static string $elementClassName = DTO::class;

    /** @psalm-suppress InvalidArgument */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function getElementClassName(): string
    {
        return static::$elementClassName;
    }
}
