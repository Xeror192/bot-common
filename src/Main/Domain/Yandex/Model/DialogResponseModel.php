<?php

namespace Jefero\Bot\Main\Domain\Yandex\Model;

use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;

class DialogResponseModel extends AbstractDialogResponseModel
{
    protected string $version;
    protected bool $endSession = false;

    public array $actionInfo = [];

    public function reload($text, $buttons = [], $params = []): AbstractDialogResponseModel
    {
        $this->text = $text;
        $this->actionInfo['answer'] = $text;
        $this->buttons = $buttons;

        if (isset($params['version'])) {
            $this->version = $params['version'];
        }

        return $this;
    }

    public function getResponse(string $chatId): array
    {
        return [
            'response' => [
                'text' => $this->text,
                'tts' => $this->text,
                'buttons' => [],
                'end_session' => $this->endSession
            ],
            'version' => $this->version
        ];
    }

    public function isEmpty(): bool
    {
        return !$this->text;
    }

    public function setImages(array $images): AbstractDialogResponseModel
    {
        return $this;
    }
}