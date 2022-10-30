<?php

namespace Jefero\Bot\Main\Domain\Common\Service\Dialog;

use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\SystemObserver;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;

class SystemDialog extends Dialog
{
    const CODE = "system";

    public function helpVoid(): DialogResponseModel
    {
        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => SystemObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => false
        ];

        $response->reload('Чтобы я могла вам помочь скажите мне нужна помощь');

        return $response;
    }
    
    public function start(): AbstractDialogResponseModel
    {
        return $this->getResponseModel()->reload("Hello");
    }

    public static function getCode(): string
    {
        return self::CODE;
    }
}