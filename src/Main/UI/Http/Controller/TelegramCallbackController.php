<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Common\UI\Http\JsonApiController;
use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\Telegram\CallbackCommand;
use Jefero\Bot\Main\Application\Telegram\CallbackTextCommand;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Telegram\Service\Telegram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class TelegramCallbackController extends BaseCallbackController
{
    protected CallbackHandler $handler;

    protected string $telegramChatId;

    public function __construct(CallbackHandler $handler, string $telegramChatId)
    {
        $this->handler = $handler;
        $this->telegramChatId = $telegramChatId;
    }

    function getCommand(Request $request): CallbackCommandInterface
    {
        if ($request->request->get("callback_query")) {
            return DTOFactory::createDtoFromRequest(CallbackCommand::class, $request);
        }

        return  DTOFactory::createDtoFromRequest(CallbackTextCommand::class, $request);
    }

    function handle(CallbackCommandInterface $command): AbstractDialogResponseModel
    {
        return $this->handler->handle($command, Telegram::CODE_TYPE);
    }

    function getResponse(): Response
    {
        return new Response();
    }

    public function invoke(Request $request): Response
    {
        if (empty($request->request->all())) {
            return $this->getResponse();
        }

        $command = $this->getCommand($request);

        if ($command->getChatId() == $this->telegramChatId) {
            return $this->getResponse();
        }

        $this->handle($command);

        return $this->getResponse();
    }
}