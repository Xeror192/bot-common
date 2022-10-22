<?php

namespace Jefero\Bot\Main\Application\VK;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Domain\VK\DTO\VKObject;
use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class NewMessageCommand extends DTO implements CallbackCommandInterface
{

    /**
     * @OA\Property(property = "group_id", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $group_id;

    /**
     * @OA\Property(property = "type", type = "string",
     *  example = "message_new",
     * )
     */
    public ?string $type = "";

    /**
     * @OA\Property(property = "event_id", type = "string",
     *  example = "b3c6c72620909cfbc5efe400efe406b618d8dc12",
     * )
     */
    public ?string $event_id = "";

    /**
     * @OA\Property(property = "v", type = "string",
     *  example = "5.1",
     * )
     */
    public ?string $v = "";

    /**
     * @OA\Property(property = "object", type = "object",
     *  ref = @Model(type = VKObject::class),
     * )
     */
    public VKObject $object;

    public function getMessage(): string
    {
        return $this->object->message->text;
    }

    public function getChatId(): string
    {
        return $this->object->message->from_id;
    }

    public function getPhoto(): array
    {
        return [];
    }
}
