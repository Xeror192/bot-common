<?php

namespace Jefero\Bot\Main\Domain\Yandex\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Meta extends DTO
{
    /**
     * @OA\Property(property = "locale", type = "string",
     *  example = "ru-RU",
     * )
     */
    public ?string $locale = "";

    /**
     * @OA\Property(property = "timezone", type = "string",
     *  example = "UTC",
     * )
     */
    public ?string $timezone = "";

    /**
     * @OA\Property(property = "client_id", type = "string",
     *  example = "UTC",
     * )
     */
    public ?string $client_id = "";
}