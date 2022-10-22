<?php

namespace Jefero\Bot\Main\Application\Yandex;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Domain\Yandex\DTO\Meta;
use Jefero\Bot\Main\Domain\Yandex\DTO\Request;
use Jefero\Bot\Main\Domain\Yandex\DTO\Session;
use Jefero\Bot\Common\DTO\DTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackCommand extends DTO implements CallbackCommandInterface
{
    /**
     * @OA\Property(property = "version", type = "string",
     *  example = "1.0",
     * )
     *
     * @Assert\NotBlank
     */
    public string $version;

    /**
     * @OA\Property(property = "meta", type = "object",
     *  ref = @Model(type = Meta::class),
     * )
     */
    public Meta $meta;

    /**
     * @OA\Property(property = "session", type = "object",
     *  ref = @Model(type = Session::class),
     * )
     */
    public Session $session;

    /**
     * @OA\Property(property = "request", type = "object",
     *  ref = @Model(type = Request::class),
     * )
     */
    public Request $request;

    public function getMessage(): string
    {
        return mb_strtolower($this->request->command);
    }

    public function getChatId(): string
    {
        return $this->session->user_id;
    }

    public function getPhoto(): array
    {
        return [];
    }
}