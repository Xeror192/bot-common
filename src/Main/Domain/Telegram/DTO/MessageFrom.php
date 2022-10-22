<?php

namespace Jefero\Bot\Main\Domain\Telegram\DTO;

use Jefero\Bot\Common\DTO\DTO;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class MessageFrom extends DTO
{
    /**
     * @OA\Property(property = "id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $id;

    /**
     * @OA\Property(property = "is_bot", type = "bool",
     *  example = "false",
     * )
     */
    public bool $is_bot;

    /**
     * @OA\Property(property = "first_name", type = "string",
     *  example = "Alexandr",
     * )
     */
    public ?string $first_name;

    /**
     * @OA\Property(property = "last_name", type = "string",
     *  example = "Onikiychuk",
     * )
     */
    public ?string $last_name = "";

    /**
     * @OA\Property(property = "username", type = "string",
     *  example = "xeror",
     * )
     */
    public ?string $username;

    /**
     * @OA\Property(property = "language_code", type = "string",
     *  example = "ru",
     * )
     */
    public ?string $language_code = "";
}
