<?php

namespace Jefero\Bot\Main\Application\Callback;

interface CallbackCommandInterface
{
    public function getMessage(): string;
    public function getChatId(): string;
    public function getPhoto(): array;
}
