<?php

namespace Jefero\Bot\Main\Domain\VK\DTO;

use Jefero\Bot\Common\DTO\DTO;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class VKObjectMessage extends DTO
{
    /**
     * @OA\Property(property = "date", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $date;

    /**
     * @OA\Property(property = "from_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $from_id;

    /**
     * @OA\Property(property = "id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $id;

    /**
     * @OA\Property(property = "id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $out;

    /**
     * @OA\Property(property = "attachments", type = "array",
     *  example = "907268840",
     * )
     */
    public array $attachments = [];

    /**
     * @OA\Property(property = "conversation_message_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $conversation_message_id;

    /**
     * @OA\Property(property = "fwd_messages", type = "array",
     *  example = "907268840",
     * )
     */
    public array $fwd_messages = [];

    /**
     * @OA\Property(property = "important", type = "bool",
     *  example = "false",
     * )
     */
    public bool $important;

    /**
     * @OA\Property(property = "is_hidden", type = "int",
     *  example = "907268840",
     * )
     */
    public bool $is_hidden;

    /**
     * @OA\Property(property = "peer_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $peer_id;

    /**
     * @OA\Property(property = "random_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $random_id;

    /**
     * @OA\Property(property = "text", type = "string",
     *  example = "907268840",
     * )
     */
    public ?string $text;
}
