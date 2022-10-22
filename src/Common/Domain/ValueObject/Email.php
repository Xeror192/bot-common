<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Email
 *
 * @ORM\Embeddable()
 */
class Email
{
    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private string $email;

    public function __construct(string $email)
    {
        // TODO Check format
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function validate(): bool
    {
        return true;
    }
}
