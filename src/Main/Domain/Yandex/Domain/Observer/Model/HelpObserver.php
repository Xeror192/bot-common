<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Observer;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Servant\Model\HelpServant;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\EmotionRepository;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\Problem\ProblemService;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Doctrine\ORM\QueryBuilder;

class HelpObserver extends Observer
{
    protected static $instance = null;

    private ?EmotionRepository $emotionRepository;
    private ?ProblemService $problemService;

    public function setEmotionRepository(EmotionRepository $emotionRepository)
    {
        $this->emotionRepository = $emotionRepository;
    }
    public function setProblemService(ProblemService $problemService)
    {
        $this->problemService = $problemService;
    }

    const CODE = 'help',
        UUID = '4fb81a91-5519-424c-b6ea-f073f7ad25b0';

    public function enable(QueryBuilder $queryBuilder): QueryBuilder
    {
        $query = $queryBuilder->getParameter("query")->getValue();
        $queryBuilder->orWhere("observer.uuid = :helpObserverId AND (requestAction.query like :observerQuery) OR (requestAction.query = :query)")
            ->setParameter("observerQuery", "$query%")
            ->setParameter("helpObserverId", self::UUID);
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

    public function action(RequestAction $action): DialogResponseModel
    {
        /** @var HelpServant $servant */
        $servant = $this->getServant();
        $servant->setEmotionRepository($this->emotionRepository);
        $servant->setProblemService($this->problemService);
        return $servant->action($action->getArguments());
    }

    public function continueAction(MemoryAction $action): DialogResponseModel
    {

        /** @var HelpServant $servant */
        $servant = $this->getServant();
        $servant->setEmotionRepository($this->emotionRepository);
        $servant->setProblemService($this->problemService);

        return $servant->continueAction($action);
    }
}