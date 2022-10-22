<?php

namespace Jefero\Bot\Main\UI\Http\Controller;

use Jefero\Bot\Main\Application\Yandex\CallbackCommand;
use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Jefero\Bot\Common\DTO\DTOFactory;
use Jefero\Bot\Common\UI\Http\JsonApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YandexCallbackController extends JsonApiController
{
    private CallbackHandler $handler;

    public function __construct(CallbackHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Route("/yandex/callback", name = "bot_yandex_callback", methods = {"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (empty($request->request->all())) {
            return new Response();
        }

        $command = DTOFactory::createDtoFromRequest(CallbackCommand::class, $request);

        $response = $this->handler->handle($command);
        $response->reload($response->text, [], [
            'version' => $command->version
        ]);
        return new Response(json_encode($response->getResponse('')));
    }
}
