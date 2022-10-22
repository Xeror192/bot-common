<?php

namespace Jefero\Bot\Main\Application\Telegram;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Domain\Telegram\DTO\CallbackQuery;
use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackCommand extends DTO implements CallbackCommandInterface
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
     * @OA\Property(property = "callback_query", type = "object",
     *  ref = @Model(type = CallbackQuery::class),
     * )
     */
    public CallbackQuery $callback_query;


    public function getMessage(): string
    {
        return $this->callback_query->data;
    }

    public function getChatId(): string
    {
        return $this->callback_query->message->chat->id;
    }

    public function getPhoto(): array
    {
        return [];
    }
}
