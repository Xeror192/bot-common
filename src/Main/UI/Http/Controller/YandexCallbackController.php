<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\Yandex\CallbackCommand;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Yandex\Service\Yandex;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class YandexCallbackController extends BaseCallbackController implements CallbackInterface
{
    protected CallbackHandler $handler;

    public function __construct(CallbackHandler $handler)
    {
        $this->handler = $handler;
    }

    function getCommand(Request $request): CallbackCommandInterface
    {
        return DTOFactory::createDtoFromRequest(CallbackCommand::class, $request);
    }

    function handle(CallbackCommandInterface $command): AbstractDialogResponseModel
    {
        /** @var CallbackCommand $command */
        $response = $this->handler->handle($command, Yandex::CODE_TYPE);
        $response->reload($response->text, [], [
            'version' => $command->version
        ]);
        
        return $response;
    }

    function getResponse(): Response
    {
        return new Response("ok");
    }
    
    public function invoke(Request $request): Response
    {
        if (empty($request->request->all())) {
            return new Response();
        }

        /** @var CallbackCommand $command */
        $command = $this->getCommand($request);

        $response = $this->handle($command);

        return new Response(json_encode($response->getResponse('')));
    }
}