<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\VK\NewMessageCommand;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\VK\Service\VKClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class VKCallbackController extends BaseCallbackController implements CallbackInterface
{
    protected CallbackHandler $handler;

    public function __construct(CallbackHandler $handler)
    {
        $this->handler = $handler;
    }

    function getCommand(Request $request): CallbackCommandInterface
    {
        return DTOFactory::createDtoFromRequest(NewMessageCommand::class, $request);
    }

    function handle(CallbackCommandInterface $command): AbstractDialogResponseModel
    {
        return $this->handler->handle($command, VKClient::CODE_TYPE);
    }

    function getResponse(): Response
    {
        return new Response("ok");
    }
    
    public function invoke(Request $request): Response
    {
        $command = $this->getCommand($request);

        $this->handle($command);

        return $this->getResponse();
    }
}