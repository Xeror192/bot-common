<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Model;

use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\SystemObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryUser;
use Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Servant;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Jefero\Bot\Common\Infrastructure\Helper\DateHelper;

class SystemServant extends Servant
{
    private function revertQuestion(): DialogResponseModel
    {
        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => SystemObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => false
        ];
        if(CallbackHandler::$command->getMessage() != 'Да') {
            $response->reload(CallbackHandler::$currentAction->getPreviousAnswer()->query);
            return $response;
        }

        $response->reload('Привет, я твой помощник, меня зовут Диана, очень приятно. ' .
            'Если хочешь узнать как я могу тебе помочь, спроси что ты умеешь или скажи помощь');
        CallbackHandler::$currentUser = MemoryUser::create();


        return $response;
    }

    public function greeting(): DialogResponseModel
    {
        if(CallbackHandler::$currentAction && CallbackHandler::$currentAction->needAnswer) {
            return $this->revertQuestion();
        }

        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => SystemObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => false
        ];

        if (!CallbackHandler::$currentAction) {
            $response->reload('Привет, я твой помощник, меня зовут Диана, очень приятно. ' .
                'Если хочешь узнать как я могу тебе помочь, спроси что ты умеешь или скажи помощь');

            return $response;
        }
        $previousAction = CallbackHandler::$currentAction->getPreviousAction();

        if ($previousAction) {
            $diffInSeconds = DateHelper::getDateDiffInSeconds(new \DateTimeImmutable(), $previousAction->lastDate);

            if ($diffInSeconds > 3600) {
                $response->reload('Рада тебя снова видеть. Чем тебе помочь');

                return $response;
            }


            if (!$previousAction->isGreeting()) {
                $response->actionInfo = [
                    'observer' => SystemObserver::UUID,
                    'action' => __FUNCTION__,
                    'needAnswer' => true
                ];
                $response->reload('Ты хочешь начать сначала?');
                return $response;
            }
        }

        $response->reload('Привет, я твой помощник, меня зовут Диана, очень приятно. ' .
            'Если хочешь узнать как я могу тебе помочь, спроси что ты умеешь или скажи помощь');

        return $response;
    }

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

    public function help(): DialogResponseModel
    {
        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => SystemObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => false
        ];

        $response->reload('Я могу помочь тебе разобраться с твоими проблемами, что бы начать скажи мне нужна помощь');

        return $response;
    }
}