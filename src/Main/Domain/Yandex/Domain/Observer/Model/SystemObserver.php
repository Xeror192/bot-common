<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Observer;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Doctrine\ORM\QueryBuilder;

class SystemObserver extends Observer
{
    protected static $instance = null;

    const CODE = 'system',
        UUID = 'e08447b3-6231-494b-b062-19fe0a37429b';

    public function enable(QueryBuilder $queryBuilder): QueryBuilder
    {
        $query = $queryBuilder->getParameter("query")->getValue();
        $queryBuilder->orWhere("observer.uuid = :systemObserverId AND (requestAction.query like :observerQuery) OR (requestAction.query = :query)")
            ->setParameter("observerQuery", "$query%")
            ->setParameter("systemObserverId", self::UUID);
        return $queryBuilder;
    }

    public function getPriority(RequestAction $action, $query): int
    {
        $actionQuery = $action->getQuery();

        if ($actionQuery == $query) {
            return 1;
        }

        $words = explode(" ", $actionQuery);
        $weight = 0;
        foreach ($words as $word) {
            if (strpos($query, $word) !== false) {
                $weight += strlen($word);
            }
        }

        return $weight / strlen($actionQuery);
    }
}