<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CallbackInterface
{
    function getCommand(Request $request): CallbackCommandInterface;
    function handle(CallbackCommandInterface $command);
    function getResponse(): Response;
}