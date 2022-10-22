<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\Telegram\CallbackCommand;
use Jefero\Bot\Main\Application\Telegram\CallbackTextCommand;
use Jefero\Bot\Main\Domain\Telegram\Service\Telegram;
use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Common\Infrastructure\Persistence\RedisRepository;
use Jefero\Bot\Common\UI\Http\JsonApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramCallbackController extends JsonApiController
{
    private CallbackHandler $handler;

    private RedisRepository $redisRepository;

    private string $telegramChatId;

    public function __construct(CallbackHandler $handler, string $telegramChatId, RedisRepository $redisRepository)
    {
        $this->handler = $handler;
        $this->redisRepository = $redisRepository;
        $this->telegramChatId = $telegramChatId;
    }

    /**
     * @Route("/telegram/callback", name = "bot_telegram_callback", methods = {"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (empty($request->request->all())) {
            return new Response();
        }

        if ($request->request->get("callback_query")) {
            $command = DTOFactory::createDtoFromRequest(CallbackCommand::class, $request);
        } else {
            $command = DTOFactory::createDtoFromRequest(CallbackTextCommand::class, $request);
        }

        if ($command->getChatId() == $this->telegramChatId) {
            return new Response();
        }

        $this->handler->handle($command, Telegram::CODE_TYPE);

        return new Response();
    }
}
