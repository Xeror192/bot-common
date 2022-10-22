<?php

namespace Jefero\Bot\Main\Domain\Telegram\DTO;

use Jefero\Bot\Common\DTO\DTO;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class MessageEntity extends DTO
{
    /**
     * @OA\Property(property = "offset", type = "int",
     *  example = "0",
     * )
     *
     * @Assert\NotBlank
     */
    public int $offset;

    /**
     * @OA\Property(property = "length", type = "int",
     *  example = "6",
     * )
     *
     * @Assert\NotBlank
     */
    public int $length;

    /**
     * @OA\Property(property = "type", type = "string",
     *  example = "private",
     * )
     */
    public string $bot_command;
}
