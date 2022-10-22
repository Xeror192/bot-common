<?php

namespace Jefero\Bot\Main\Domain\Yandex\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Request extends DTO
{
    /**
     * @OA\Property(property = "command", type = "string",
     *  example = "d94ebcd9-df39-46f5-9ff4-30f82d4b891d",
     * )
     */
    public ?string $command = "";

    /**
     * @OA\Property(property = "original_utterance", type = "string",
     *  example = "d94ebcd9-df39-46f5-9ff4-30f82d4b891d",
     * )
     */
    public ?string $original_utterance = "";

    /**
     * @OA\Property(property = "type", type = "string",
     *  example = "SimpleUtterance",
     * )
     */
    public ?string $type = "";
}