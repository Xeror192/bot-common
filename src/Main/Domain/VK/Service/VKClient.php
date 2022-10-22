<?php

namespace Jefero\Bot\Main\Domain\VK\Service;

use VK\Client\VKApiClient;

class VKClient
{
    const CODE_TYPE = "vk";

    private VKApiClient $client;

    private string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->client = new VKApiClient();
        $this->accessToken = $accessToken;
    }

    public function sendMessage($chatId, $text)
    {
    }

    public function sendMediaMessage($chatId, $text, $medias)
    {
    }

    public function sendMessageToPublic($text)
    {
    }

    public function sendMediaMessageToPublic($text, $media)
    {
    }

    public function sendRawMessage(array $request)
    {
        return $this->client->messages()->send($this->accessToken, $request);
    }
}
