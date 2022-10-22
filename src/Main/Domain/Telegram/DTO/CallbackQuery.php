<?php

namespace Jefero\Bot\Main\Domain\Telegram\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackQuery extends DTO
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
     * @OA\Property(property = "from", type = "object",
     *  ref = @Model(type = MessageFrom::class),
     * )
     */
    public MessageFrom $from;

    /**
     * @OA\Property(property = "message", type = "object",
     *  ref = @Model(type = Message::class),
     * )
     */
    public Message $message;

    /**
     * @OA\Property(property = "chat_instance", type = "string",
     *  example = "1634807633",
     * )
     */
    public ?string $chat_instance = "";

    /**
     * @OA\Property(property = "data", type = "string",
     *  example = "1634807633",
     * )
     */
    public ?string $data = "";
}
