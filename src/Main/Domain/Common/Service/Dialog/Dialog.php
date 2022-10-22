<?php

namespace Jefero\Bot\Main\Domain\Common\Service\Dialog;

use Jefero\Bot\Main\Application\Callback\CallbackCommandInterface;
use Jefero\Bot\Main\Application\Callback\CallbackHandler;
use Jefero\Bot\Main\Application\VK\NewMessageCommand;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Telegram\Model\DialogResponseModel as TelegramDialogResponseModel;
use Jefero\Bot\Main\Domain\Telegram\Service\Telegram;
use Jefero\Bot\Main\Domain\VK\Model\DialogResponseModel as VKDialogResponseModel;
use Jefero\Bot\Main\Domain\VK\Service\VKClient;

abstract class Dialog
{
    private ?CallbackHandler $callbackHandler;

    public function setCallbackHandler(CallbackHandler $callbackHandler): static
    {
        $this->callbackHandler = $callbackHandler;

        return $this;
    }

    public function getCallback()
    {
        return $this->callbackHandler;
    }

    public function end(): void
    {
        $this->callbackHandler->clear();
    }

    public function getMessage(): CallbackCommandInterface
    {
        return $this->callbackHandler->getMessage();
    }

    public function getText(): ?string
    {
        return $this->getMessage()->getMessage();
    }

    public function withNewAttachment(): bool
    {
        return !empty($this->getMessage()->getPhoto());
    }

    public function getResponseModel(): AbstractDialogResponseModel
    {
        if ($this->callbackHandler->getType() == Telegram::CODE_TYPE) {
            if (!$this->callbackHandler->getResponseModel()) {
                $this->callbackHandler->setResponseModel(TelegramDialogResponseModel::mock());
            }

            return $this->callbackHandler->getResponseModel();
        }

        if ($this->callbackHandler->getType() == VKClient::CODE_TYPE) {
            /** @var NewMessageCommand $message */
            $message = $this->callbackHandler->getMessage();
            /** @var VKDialogResponseModel $model */
            $model = VKDialogResponseModel::mock();
            $model->setPeerId($message->object->message->peer_id);
            $model->setGroupId($message->group_id);
            return $model;
        }

        return TelegramDialogResponseModel::mock();
    }

    abstract public static function getCode(): string;
}