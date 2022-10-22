<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Servant;

use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;

abstract class Servant
{
    public function action($arguments): ?DialogResponseModel
    {
        if (!isset($arguments["action"])) {
            return null;
        }

        if (!method_exists(static::class, $arguments["action"])) {
            return null;
        }

        $ReflectionMethod = new \ReflectionMethod(static::class, $arguments["action"]);

        $params = $ReflectionMethod->getParameters();

        $names = array_map(function ($item) {
            return $item->getName();
        }, $params);

        $methodArguments = [];
        foreach ($names as $name) {
            if (isset($arguments[$name])) {
                $methodArguments[] = json_decode($arguments[$name]) ? json_decode($arguments[$name]) : $arguments[$name];
            }
        }

        return call_user_func_array([$this, $arguments["action"]], $methodArguments);
    }
    public function continueAction(MemoryAction $action): ?DialogResponseModel
    {
        $arguments = $action->getArguments();

        if (!method_exists(static::class, $action->action)) {
            return null;
        }

        $ReflectionMethod = new \ReflectionMethod(static::class, $action->action);

        $params = $ReflectionMethod->getParameters();

        $names = array_map(function ($item) {
            return $item->getName();
        }, $params);

        $methodArguments = [];
        foreach ($names as $name) {
            if (isset($arguments[$name])) {
                $methodArguments[] = json_decode($arguments[$name]) ? json_decode($arguments[$name]) : $arguments[$name];
            }
        }

        return call_user_func_array([$this, $action->action], $methodArguments);
    }
}