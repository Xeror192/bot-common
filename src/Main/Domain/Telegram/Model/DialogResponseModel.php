<?php

namespace Jefero\Bot\Main\Domain\Telegram\Model;

use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;

class DialogResponseModel extends AbstractDialogResponseModel
{
    public function getResponse(string $chatId): array
    {
        if ($this->isMedia()) {
            $response = [
                "chat_id" => $chatId,
                'media' => []
            ];

            foreach ($this->images as $image) {
                $response['media'][] = [
                    'type' => 'photo',
                    'media' => $image
                ];
            }

            $response['media'] = json_encode($response['media']);

            return $response;
        }

        $response = [
            "chat_id" => $chatId,
            "text" => $this->text,
            'parse_mode' => 'MarkdownV2'
        ];

        if (!empty($this->buttons)) {
            $buttons = [];
            foreach ($this->buttons as $button) {
                $buttons[] = [["text" => $button, "callback_data" => $button]];
            }

            $response["reply_markup"] = json_encode([
                "inline_keyboard" => $buttons
            ]);
        }
        return $response;
    }

    public function reload($text, $buttons = [], $params = []): AbstractDialogResponseModel
    {
        $this->text = $text;
        $this->buttons = $buttons;

        return $this;
    }

    public function setImages(array $images = []): AbstractDialogResponseModel
    {
        $this->images = $images;

        return $this;
    }
    
    public function isNeedSendMessage(): bool
    {
        return true;
    }
}
