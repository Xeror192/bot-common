<?php

namespace Jefero\Bot\Main\Application\Telegram;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Domain\Telegram\DTO\Message;
use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackTextCommand extends DTO implements CallbackCommandInterface
{
    /**
     * @OA\Property(property = "update_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $update_id;

    /**
     * @OA\Property(property = "message", type = "object",
     *  ref = @Model(type = Message::class),
     * )
     */
    public Message $message;

    public function getMessage(): string
    {
        return $this->message->text;
    }

    public function getChatId(): string
    {
        return $this->message->chat->id;
    }

    public function getPhoto(): array
    {
        return $this->message->photo;
    }
}
