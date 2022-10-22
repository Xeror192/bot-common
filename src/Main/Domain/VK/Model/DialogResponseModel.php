<?php

namespace Jefero\Bot\Main\Domain\VK\Model;

use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;

class DialogResponseModel extends AbstractDialogResponseModel
{
    private int $peerId;

    private int $userId;

    private int $groupId;

    public function getResponse(string $chatId): array
    {
        $buttons = [];

        foreach ($this->buttons as $button) {
            $buttons[] = [
                [
                    "action" => [
                        "type" => "text",
                        "label" => $button,
                        "payload" => "{}"
                    ]
                ]
            ];
        }
        return [
            "user_id" => $chatId,
            "random_id" => random_int(10000, 99999),
            "peer_id" => "-" . $this->peerId,
            "chat_id" => $chatId,
            "message" => $this->text,
            "keyboard" => json_encode([
                "one_time" => false,
                "buttons" => $buttons,
                "inline" => false
            ]),
            "group_id" => $this->groupId
        ];
    }

    public function reload($text, $buttons = [], $params = []): AbstractDialogResponseModel
    {
        $this->text = $text;
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @param int $peerId
     */
    public function setPeerId(int $peerId): void
    {
        $this->peerId = $peerId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function setImages(array $images): AbstractDialogResponseModel
    {
        return $this;
    }
}
