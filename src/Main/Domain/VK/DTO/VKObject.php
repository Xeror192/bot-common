<?php

namespace Jefero\Bot\Main\Domain\VK\DTO;

use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class VKObject extends DTO
{
    /**
     * @OA\Property(property = "message", type = "object",
     *  ref = @Model(type = VKObjectMessage::class),
     * )
     */
    public VKObjectMessage $message;

    /**
     * @OA\Property(property = "client_info", type = "object",
     *  ref = @Model(type = VKObjectClientInfo::class),
     * )
     */
    public VKObjectClientInfo $client_info;
}
