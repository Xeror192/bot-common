<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\UI\Http\EventSubscriber;

use Jefero\Bot\Common\UI\Http\JsonApiResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonBodyParserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isJsonRequest($request)) {
            return;
        }

        $content = $request->getContent();
        if (empty($content)) {
            return;
        }

        if (!$this->transformJsonBody($request)) {
            $response = JsonApiResponse::fail('Unable to parse json request.', Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request): bool
    {
        return 'json' === $request->getContentType();
    }

    private function transformJsonBody(Request $request): bool
    {
        try {
            $data = \json_decode(
                (string) $request->getContent(),
                true,
                512,
                \JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            return false;
        }

        if (null === $data) {
            return true;
        }

        $request->request->replace($data);

        return true;
    }
}
