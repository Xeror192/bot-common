<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Phone
 *
 * @ORM\Embeddable()
 */
class Phone
{
    const DEFAULT_COUNTRY_CODE = "RU",
    COUNTRY_CODES = [
        "RU" => "7"
    ];

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private string $phone;

    public function __construct(string $phone, string $countryCode = self::DEFAULT_COUNTRY_CODE)
    {
        // TODO Check format
        $this->phone = self::COUNTRY_CODES[$countryCode] . mb_strtolower($phone);
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    public function validate(): bool
    {
        return true;
    }
}
