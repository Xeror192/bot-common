<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction;

use Jefero\Bot\Main\Application\Yandex\CallbackCommand;
use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\SystemObserver;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;

class MemoryAction
{
    const TYPE_ACTION = 'action',
        TYPE_ANSWER = 'answer';

    public string $observer;

    public string $type;

    public bool $needAnswer = false;

    public string $action;

    public string $query = '';

    /** @var string[] */
    public array $arguments = [];

    /** @var MemoryAction[] */
    public array $history = [];

    public \DateTimeImmutable $lastDate;

    public function __construct()
    {
        $this->lastDate = new \DateTimeImmutable();
    }

    public static function createFromMemory(array $object): self
    {
        $response = new self();
        $response->observer = $object['observer'];
        $response->needAnswer = $object['needAnswer'];
        $response->query = $object['query'];
        $response->type = $object['type'];
        $response->action = $object['action'];
        $response->arguments = $object['arguments'];
        $response->lastDate = new \DateTimeImmutable($object['lastDate']);
        $response->history = [];
        foreach ($object['history'] as $oldAction) {
            $response->history[] = self::createFromMemory($oldAction);
        }
        return $response;
    }

    public static function createFromResponse(DialogResponseModel $responseModel, CallbackCommand $command): self
    {
        return new self();
    }

    public static function createFromRequestAction(RequestAction $requestAction): self
    {
        $response = new self();
        $response->observer = $requestAction->getObserverUuid();
        $response->type = self::TYPE_ACTION;
        $response->action = $requestAction->getArguments()['action'];
        $response->query = CallbackHandler::$command->getMessage();

        return $response;
    }

    public static function createNew(string $observer, string $type, string $action): self
    {
        $response = new self();
        $response->observer = $observer;
        $response->type = $type;
        $response->action = $action;
        $response->query = CallbackHandler::$command->getMessage();

        return $response;
    }

    public static function createWithHistory(array $object): self
    {
        $oldAction = self::createFromMemory($object);
        $response = new self();
        $response->observer = SystemObserver::UUID;
        $response->action = 'greeting';
        $response->type = self::TYPE_ACTION;
        $response->query = CallbackHandler::$command->getMessage();

        $response->history = $oldAction->history;
        $oldAction->history = [];
        $response->history[] = $oldAction;


        uasort($response->history, function ($a, $b) {
            /** @var MemoryAction $a */
            /** @var MemoryAction $b */
            $firstDate = $a->lastDate->getTimestamp();
            $secondDate = $b->lastDate->getTimestamp();
            if ($firstDate == $secondDate) {
                return 0;
            }
            return ($firstDate > $secondDate) ? -1 : 1;
        });
        return $response;
    }

    public static function createGreeting(): self
    {
        $response = new self();
        $response->type = self::TYPE_ACTION;
        $response->observer = SystemObserver::UUID;
        $response->action = 'greeting';
        $response->query = CallbackHandler::$command->getMessage();

        return $response;
    }

    public static function createFromVoid(): self
    {
        $response = new self();
        $response->type = self::TYPE_ACTION;
        $response->observer = SystemObserver::UUID;
        $response->action = 'helpVoid';
        $response->query = CallbackHandler::$command->getMessage();

        return $response;
    }

    public function toArray(): array
    {
        $response = [
            'observer' => $this->observer,
            'needAnswer' => $this->needAnswer,
            'query' => $this->query,
            'type' => $this->type,
            'action' => $this->action,
            'arguments' => $this->arguments,
            'lastDate' => $this->lastDate->format('Y-m-d H:i:s'),
            'history' => []
        ];

        foreach ($this->history as $oldAction) {
            $response['history'][] = $oldAction->toArray();
        }

        uasort($response['history'], function ($a, $b) {
            $firstDate = strtotime($a['lastDate']);
            $firstDate += $a['type'] == self::TYPE_ANSWER ? 1 : 0;

            $secondDate = strtotime($b['lastDate']);
            $secondDate += $b['type'] == self::TYPE_ANSWER ? 1 : 0;

            if ($firstDate == $secondDate) {
                return 0;
            }
            return ($firstDate > $secondDate) ? -1 : 1;
        });

        return $response;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public static function addAction(MemoryAction $memoryAction, MemoryAction $currentAction): self
    {
        $memoryAction->type = self::TYPE_ACTION;
        $memoryAction->history = $currentAction->history;
        $currentAction->history = [];
        $memoryAction->history[] = $currentAction;
        uasort($memoryAction->history, function ($a, $b) {
            /** @var MemoryAction $a */
            /** @var MemoryAction $b */
            $firstDate = $a->lastDate->getTimestamp();
            $secondDate = $b->lastDate->getTimestamp();
            if ($firstDate == $secondDate) {
                return 0;
            }
            return ($firstDate > $secondDate) ? -1 : 1;
        });
        return $memoryAction;
    }

    public function getPreviousAction(): ?MemoryAction
    {
        if (empty($this->history)) {
            return null;
        }

        foreach ($this->history as $action) {
            if($action->type == self::TYPE_ACTION) {
                return $action;
            }
        }
        return null;
    }

    public function getPreviousAnswer(): ?MemoryAction
    {
        if (empty($this->history)) {
            return null;
        }

        foreach ($this->history as $action) {
            if($action->type == self::TYPE_ANSWER) {
                return $action;
            }
        }
        return null;
    }

    public function isGreeting(): bool
    {
        return $this->observer == SystemObserver::UUID && $this->action == 'greeting';
    }
//
//    public function addAnswer($params = []): self
//    {
//        $memoryAction = new self();
//        $memoryAction->type = self::TYPE_ANSWER;
//
//        $memoryAction->observer = $params['observer'];
//        $memoryAction->action = $params['action'];
//        $memoryAction->query = $params['answer'];
//        $memoryAction->needAnswer = $params['needAnswer'];
//        $memoryAction->history = $this->history;
//        $this->history = [];
//        $memoryAction->history[] = $this;
//        uasort($memoryAction->history, function ($a, $b) {
//            /** @var MemoryAction $a */
//            /** @var MemoryAction $b */
//            $firstDate = $a->lastDate->getTimestamp();
//            $firstDate += $a->type == self::TYPE_ANSWER ? 1 : 0;
//            $secondDate = $b->lastDate->getTimestamp();
//            $secondDate += $b->type == self::TYPE_ANSWER ? 1 : 0;
//            if ($firstDate == $secondDate) {
//                return 0;
//            }
//            return ($firstDate > $secondDate) ? -1 : 1;
//        });
//        return $memoryAction;
//    }

    public function addAnswer($params = []): void
    {
        $oldAction = new self();
        $oldAction->type = $this->type;
        $oldAction->observer = $this->observer;
        $oldAction->action = $this->action;
        $oldAction->query = $this->query;
        $oldAction->needAnswer = $this->needAnswer;
        $oldAction->lastDate = $this->lastDate;
        $oldAction->arguments = $this->arguments;

        $this->type = self::TYPE_ANSWER;
        $this->observer = $params['observer'];
        $this->action = $params['action'];
        $this->query = $params['answer'];
        $this->needAnswer = $params['needAnswer'];
        $this->history[] = $oldAction;

        uasort($this->history, function ($a, $b) {
            /** @var MemoryAction $a */
            /** @var MemoryAction $b */
            $firstDate = $a->lastDate->getTimestamp();
            $firstDate += $a->type == self::TYPE_ANSWER ? 1 : 0;
            $secondDate = $b->lastDate->getTimestamp();
            $secondDate += $b->type == self::TYPE_ANSWER ? 1 : 0;
            if ($firstDate == $secondDate) {
                return 0;
            }
            return ($firstDate > $secondDate) ? -1 : 1;
        });
    }
}