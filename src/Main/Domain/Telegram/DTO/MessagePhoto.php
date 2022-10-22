<?php

namespace Jefero\Bot\Main\Domain\Telegram\DTO;

use Jefero\Bot\Common\DTO\DTO;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class MessagePhoto extends DTO
{
    /**
     * @OA\Property(property = "file_id", type = "string",
     *  example = "AgACAgIAAxkBAANRYXFbC0YRt4hDfCBQhYSEHtbMJVgAAgG2MRvhBpFL0JsfQHOSdOwBAAMCAANzAAMhBA",
     * )
     * @Assert\NotBlank
     */
    public string $file_id;

    /**
     * @OA\Property(property = "file_unique_id", type = "string",
     *  example = "AQADAbYxG-EGkUt4",
     * )
     * @Assert\NotBlank
     */
    public string $file_unique_id;

    /**
     * @OA\Property(property = "file_size", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $file_size;

    /**
     * @OA\Property(property = "width", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $width;

    /**
     * @OA\Property(property = "height", type = "int",
     *  example = "907268840",
     * )
     *
     * @Assert\NotBlank
     */
    public int $height;
}
