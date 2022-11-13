<?php

namespace Jefero\Bot\Main\Domain\Common\Model;

use Jefero\Bot\Main\Application\Yandex\CallbackCommand;
use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Common\Service\Dialog\SystemDialog;
use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\SystemObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Monolog\DateTimeImmutable;

class RedisBagAction
{
    const TYPE_ACTION = 'action',
        TYPE_ANSWER = 'answer';

    public string $type;
    
    public string $code;

    public bool $needAnswer = false;
    
    public bool $greeting = false;

    public string $action;

    public string $query = '';

    /** @var string[] */
    public array $arguments = [];

    /** @var RedisBagAction[] */
    public array $history = [];

    public \DateTimeImmutable $lastDate;

    public function __construct()
    {
        $this->lastDate = new \DateTimeImmutable();
    }

    public static function createFromMemory(array $object): self
    {
        $response = new self();
        $response->code = $object['code'];
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

    public static function createNew(string $code, string $type, string $action, string $query, bool $needAnswer = false): self
    {
        $response = new self();
        $response->code = $code;
        $response->type = $type;
        $response->action = $action;
        $response->query = $query;
        $response->needAnswer = $needAnswer;

        return $response;
    }

    public static function creatAction(string $code, string $action, string $query, bool $needAnswer = false): self
    {
        return self::createNew($code, self::TYPE_ACTION, $action, $query, $needAnswer);
    }

    public static function creatAnswer(string $code, string $action, string $query, bool $needAnswer = false): self
    {
        return self::createNew($code, self::TYPE_ANSWER, $action, $query, $needAnswer);
    }

    public static function creatQuestion(string $code, string $action, string $query): self
    {
        return self::createNew($code, self::TYPE_ANSWER, $action, $query, true);
    }

    public static function createWithHistory(array $object): self
    {
        $oldAction = self::createFromMemory($object);
        $response = new self();
        $response->code = \Jefero\Bot\Main\Application\Callback\CallbackHandler::$mainCode;
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

    public static function createGreeting(string $query): self
    {
        $response = new self();
        $response->type = self::TYPE_ACTION;
        $response->observer = \Jefero\Bot\Main\Application\Callback\CallbackHandler::$mainCode;
        $response->action = 'start';
        $response->query = $query;

        return $response;
    }

    public static function createFromVoid(string $query): self
    {
        $response = new self();
        $response->type = self::TYPE_ACTION;
        $response->code = \Jefero\Bot\Main\Application\Callback\CallbackHandler::$mainCode;
        $response->action = 'start';
        $response->query = $query;
        $response->greeting = true;

        return $response;
    }

    public function toArray(): array
    {
        $response = [
            'code' => $this->code,
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

    public function getPreviousAction(): ?RedisBagAction
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

    public function getPreviousAnswer(): ?RedisBagAction
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

    public function add(?RedisBagAction $action): void
    {
        if (!$action) {
            return;
        }
        
        $oldAction = new self();
        $oldAction->type = $this->type;
        $oldAction->code = $this->code;
        $oldAction->action = $this->action;
        $oldAction->query = $this->query;
        $oldAction->needAnswer = $this->needAnswer;
        $oldAction->lastDate = $this->lastDate;
        $oldAction->arguments = $this->arguments;

        $this->type = $action->type;
        $this->code = $action->code;
        $this->action = $action->action;
        $this->query = $action->query;
        $this->needAnswer = $action->needAnswer;
        $this->lastDate = new \DateTimeImmutable();
        $this->history[] = $oldAction;

        uasort($this->history, function ($a, $b) {
            /** @var RedisBagAction $a */
            /** @var RedisBagAction $b */
            $firstDate = $a->lastDate->getTimestamp();
            $firstDate += $a->type == self::TYPE_ANSWER ? 1 : 0;
            $secondDate = $b->lastDate->getTimestamp();
            $secondDate += $b->type == self::TYPE_ANSWER ? 1 : 0;
            if ($firstDate == $secondDate) {
                return 0;
            }
            return ($firstDate > $secondDate) ? -1 : 1;
        });
        $this->history = array_values($this->history);
    }
    
    public function setParameter(string $name, $value): self
    {
        $this->arguments[$name] = $value;
        
        return $this;
    }

    public function getParameter(string $name)
    {
        if (isset($this->arguments[$name])) {
            return $this->arguments[$name];
        }

        return null;
    }
    
    public function isGreeting(): bool
    {
        return $this->greeting;
    }
}