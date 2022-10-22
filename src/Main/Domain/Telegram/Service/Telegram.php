<?php

namespace Jefero\Bot\Main\Domain\Telegram\Service;

use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class Telegram
{
    const CODE_TYPE = "telegram";

    private DoctrineRepository $doctrineRepository;

    private Api $api;

    public function __construct(DoctrineRepository $doctrineRepository, string $apiKey)
    {
        $this->api = new Api($apiKey);
        $this->doctrineRepository = $doctrineRepository;
    }

    public function sendLocation(string $chatId, float $latitude, float $longitude): Message
    {
        return $this->api->sendLocation([
            "chat_id" => $chatId,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "parse_mode" => "MarkdownV2"
        ]);
    }

    public function sendMessage($chatId, $text): Message
    {
        return $this->api->sendMessage([
            "chat_id" => $chatId,
            "text" => $text,
            "parse_mode" => "MarkdownV2"
        ]);
    }

    public function sendRawMessage(array $request): Message
    {
        return $this->api->sendMessage($request);
    }

    public function sendMediaMessage(array $request): void
    {
        $step = 0;
        $newRequest = $request;

        while(!empty($newRequest['media'])) {
            $newRequest['media'] = json_encode(array_slice(json_decode($request['media']), $step, 10));

            if (empty(array_slice(json_decode($request['media']), $step, 10))) {
                break;
            }
            $this->api->sendMediaGroup($newRequest);
            $step += 10;
        }
    }
}
