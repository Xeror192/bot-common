<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Observer;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Servant;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Doctrine\ORM\QueryBuilder;

abstract class Observer
{
    const CODE = '';

    protected static $instance = null;

    public ?Servant $servant = null;

    public static function getInstance(): static
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function action(RequestAction $action): DialogResponseModel
    {
        return $this->getServant()->action($action->getArguments());
    }

    public function continueAction(MemoryAction $action): DialogResponseModel
    {
        return $this->getServant()->continueAction($action);
    }

    abstract public function enable(QueryBuilder $queryBuilder);

    abstract public function getPriority(RequestAction $action, $query): int;

    public function getServant()
    {
        if ($this->servant) {
            return $this->servant;
        }

        $servant = "Jefero\\Bot\\Main\\Domain\\Yandex\\Domain\\Servant\\Model\\" . ucfirst(static::CODE) . "Servant";

        $this->servant = new $servant;

        return $this->servant;
    }
}