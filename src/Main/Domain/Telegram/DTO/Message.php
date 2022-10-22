<?php

namespace Jefero\Bot\Main\Domain\Telegram\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Message extends DTO
{
    /**
     * @OA\Property(property = "message_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $message_id;

    /**
     * @OA\Property(property = "text", type = "string",
     *  example = "1634807633",
     * )
     */
    public ?string $text = "";

    /**
     * @OA\Property(property = "from", type = "object",
     *  ref = @Model(type = MessageFrom::class),
     * )
     */
    public MessageFrom $from;

    /**
     * @OA\Property(property = "chat", type = "object",
     *  ref = @Model(type = MessageChat::class),
     * )
     */
    public MessageChat $chat;

    /**
     * @OA\Property(property = "date", type = "int",
     *  example = "1634807633",
     * )
     *
     * @Assert\NotBlank
     */
    public int $date;

    /**
     * @OA\Property(property = "entities", type = "array", nullable = false,
     *     @OA\Items(
     *       ref= @Model(type = MessageEntity::class),
     *     ),
     * ),
     */
    public array $entities = [];

    /**
     * @OA\Property(property = "photo", type = "array", nullable = false,
     *     @OA\Items(
     *       ref= @Model(type = MessagePhoto::class),
     *     ),
     * ),
     */
    public array $photo = [];
}
