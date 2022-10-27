<?php

namespace Jefero\Bot\Main\Domain\Common\Service\Dialog;

use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Common\Model\Dialog;

class MainDialog extends Dialog
{
    const CODE = "main";

    public function start(): AbstractDialogResponseModel
    {
        return $this->getResponseModel()->reload("Hello");
    }

    public static function getCode(): string
    {
        return self::CODE;
    }

    private function getCustomer(): ?Customer
    {
        return $this->getCallback()->getCustomer($this->getCallback()->getMessage()->getChatId());
    }
}