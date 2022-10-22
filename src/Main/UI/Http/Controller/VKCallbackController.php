<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\Telegram\CallbackCommand;
use Jefero\Bot\Main\Application\Telegram\CallbackTextCommand;
use Jefero\Bot\Main\Application\VK\NewMessageCommand;
use Jefero\Bot\Main\Domain\VK\Service\VKClient;
use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Common\UI\Http\JsonApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VKCallbackController extends JsonApiController
{
    private CallbackHandler $handler;

    private string $telegramChatId;

    public function __construct(CallbackHandler $handler, string $telegramChatId)
    {
        $this->handler = $handler;
        $this->telegramChatId = $telegramChatId;
    }

    /**
     * @Route("/vk/callback", name = "bot_vk_callback", methods = {"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
//        if(empty($request->request->all())) {
//            return new Response("6a06d693");
//        }
        $command = DTOFactory::createDtoFromRequest(NewMessageCommand::class, $request);

        $this->handler->handle($command, VKClient::CODE_TYPE);

        return new Response("ok");
    }
}
