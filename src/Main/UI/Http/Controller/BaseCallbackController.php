<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Common\UI\Http\JsonApiController;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseCallbackController extends JsonApiController implements CallbackInterface
{
    public function onRequestStart(Request $request): void
    {
        
    }
    
    public function onRequestEnd(AbstractDialogResponseModel $response): void
    {
        
    }
}