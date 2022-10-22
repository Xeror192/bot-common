<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Model;

use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\HelpObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\Emotion;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryUser;
use Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Servant;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\EmotionRepository;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\Problem\ProblemService;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;

class HelpServant extends Servant
{
    private ?EmotionRepository $emotionRepository = null;
    private ?ProblemService $problemService = null;

    public function setEmotionRepository(EmotionRepository $emotionRepository)
    {
        $this->emotionRepository = $emotionRepository;
    }

    public function setProblemService(ProblemService $problemService)
    {
        $this->problemService = $problemService;
    }

    public function help(): DialogResponseModel
    {
        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => HelpObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => true
        ];

        if (!CallbackHandler::$currentUser->problem && !CallbackHandler::$currentAction->needAnswer) {
            $response->reload('Расскажите о вашей проблеме, что вас беспокоит');
            return $response;
        }

        if (!CallbackHandler::$currentUser->problem && CallbackHandler::$currentAction->needAnswer) {
            CallbackHandler::$currentUser->problem = CallbackHandler::$command->getMessage();
            $problem = $this->problemService->findProblem(CallbackHandler::$currentUser->problem);

            if ($problem) {
                CallbackHandler::$currentUser->problemCode = $problem->getCode();
            }

            $response->reload('Пожалуйста, перечислите какие эмоции вы испытываете');
            return $response;
        }

        if (CallbackHandler::$currentUser->problem && empty(CallbackHandler::$currentUser->emotions)) {
            $emotionsArray = explode(' ', CallbackHandler::$command->getMessage());

            foreach ($emotionsArray as $item) {
                $emotion = $this->emotionRepository->findByName($item);

                if ($emotion) {
                    CallbackHandler::$currentUser->emotions[] = Emotion::createFromEntity($emotion);
                }
            }
            if (!empty(CallbackHandler::$currentUser->emotions)) {
                $response->reload('Насколько вы чувствуете ' . CallbackHandler::$currentUser->emotions[0]->name . '. Оцените от одного до десяти');
                return $response;
            }
            $response->reload('Я не знаю таких эмоций');
            return $response;
        }

        if (!empty(CallbackHandler::$currentUser->emotions)) {
            $score = 0;

            if ((int)CallbackHandler::$command->getMessage()) {
                $score = (int)CallbackHandler::$command->getMessage();
            }

            if (!$score) {

                switch (CallbackHandler::$command->getMessage()) {
                    case 'один':
                    {
                        $score = 1;
                        break;
                    }
                    case 'два':
                    {
                        $score = 2;
                        break;
                    }
                    case 'три':
                    {
                        $score = 3;
                        break;
                    }
                    case 'четыре':
                    {
                        $score = 4;
                        break;
                    }
                    case 'пять':
                    {
                        $score = 5;
                        break;
                    }
                    case 'шесть':
                    {
                        $score = 6;
                        break;
                    }
                    case 'семь':
                    {
                        $score = 7;
                        break;
                    }
                    case 'восемь':
                    {
                        $score = 8;
                        break;
                    }
                    case 'девять':
                    {
                        $score = 9;
                        break;
                    }
                    case 'десять':
                    {
                        $score = 10;
                        break;
                    }
                    default:
                    {
                        foreach (CallbackHandler::$currentUser->emotions as $emotion) {
                            if (!$emotion->score) {
                                $response->reload('Я вас не понимаю. Насколько вы чувствуете ' . $emotion->name . '. Оцените от одного до десяти');
                                return $response;
                            }
                        }
                    }
                }
            }

            reset(CallbackHandler::$currentUser->emotions);
            foreach (CallbackHandler::$currentUser->emotions as $emotion) {
                if (!$emotion->score) {
                    $emotion->score = $score;
                    break;
                }
            }
            reset(CallbackHandler::$currentUser->emotions);

            foreach (CallbackHandler::$currentUser->emotions as $emotion) {
                if (!$emotion->score) {
                    $response->reload('Насколько вы чувствуете ' . $emotion->name . '. Оцените от одного до десяти');
                    return $response;
                }
            }
        }

        /** @var DialogResponseModel $response */
        $response = DialogResponseModel::create('');
        $response->actionInfo = [
            'observer' => HelpObserver::UUID,
            'action' => __FUNCTION__,
            'needAnswer' => false
        ];

        $shockScore = $this->problemService->getShockScore();
        $text = '';
        if (CallbackHandler::$currentUser->problemCode) {
            $text .= "Ваша проблема мне понятна. " . $this->problemService->getProblemDescription(CallbackHandler::$currentUser->problemCode) . ". ";
        } else {
            $text .= "Ваша проблема мне недостаточно понятна, и скорее всего, нам нужно поговорить лично.";
        }

        if ($shockScore < -10) {
            $text .= "В вашем случае, необходима шоковая терапия. Это тяжело, но очень эффективно. и я буду сопровождать вас весь курс. ";
        } elseif ($shockScore > 10) {
            $text .= "В вашем случае, думаю, хватит просто консультации с нашим психологом. ";
        } elseif ($shockScore > 50) {
            $text .= "Я думаю, у вас нет никаких проблем. ";
        }

        $text .= "В любом случае, не переживайте, все будет хорошо";
        $response->reload($text);
        CallbackHandler::$currentUser = MemoryUser::create();

        return $response;
    }
}