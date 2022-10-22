<?php

namespace Jefero\Bot\Main\Domain\VK\DTO;

use Jefero\Bot\Common\DTO\DTO;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class VKObjectClientInfo extends DTO
{
    /**
     * @OA\Property(property = "button_actions", type = "array",
     *  example = "907268840",
     * )
     */
    public array $button_actions = [];

    /**
     * @OA\Property(property = "keyboard", type = "bool",
     *  example = "true",
     * )
     */
    public bool $keyboard;

    /**
     * @OA\Property(property = "inline_keyboard", type = "bool",
     *  example = "true",
     * )
     */
    public bool $inline_keyboard;

    /**
     * @OA\Property(property = "carousel", type = "bool",
     *  example = "true",
     * )
     */
    public bool $carousel;

    /**
     * @OA\Property(property = "lang_id", type = "int",
     *  example = "0",
     * )
     *
     * @Assert\NotBlank
     */
    public int $lang_id;
}
