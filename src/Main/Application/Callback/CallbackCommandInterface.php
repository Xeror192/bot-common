<?php

namespace Jefero\Main\Bot\Application\Callback;

interface CallbackCommandInterface
{
    public function getMessage(): string;
    public function getChatId(): string;
    public function getPhoto(): array;
}
