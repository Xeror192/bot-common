<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\DTO\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    /** @var string */
    protected $message = 'Validation error';
    protected array $messages;

    public function __construct(array $messages = [], \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        $this->messages = $messages;

        parent::__construct($this->message, $previous, $code, $headers);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
