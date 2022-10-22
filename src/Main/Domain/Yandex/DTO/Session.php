<?php

namespace Jefero\Bot\Main\Domain\Yandex\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Session extends DTO
{

    /**
     * @OA\Property(property = "message_id", type = "integer",
     *  example = "0",
     * )
     */
    public int $message_id;

    /**
     * @OA\Property(property = "session_id", type = "string",
     *  example = "d94ebcd9-df39-46f5-9ff4-30f82d4b891d",
     * )
     */
    public ?string $session_id = "";

    /**
     * @OA\Property(property = "skill_id", type = "string",
     *  example = "d94ebcd9-df39-46f5-9ff4-30f82d4b891d",
     * )
     */
    public ?string $skill_id = "";

    /**
     * @OA\Property(property = "user_id", type = "string",
     *  example = "8FD292CF151173A7FB454A70023D87166617EC457F76B3C8E10A6B5BEA3F6485",
     * )
     */
    public ?string $user_id = "";

    /**
     * @OA\Property(property = "new", type = "bool",
     *  example = "true",
     * )
     */
    public bool $new = true;
}