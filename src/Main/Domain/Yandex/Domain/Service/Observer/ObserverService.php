<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service\Observer;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\HelpObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Observer;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\EmotionRepository;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\Problem\ProblemService;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\ProblemRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class ObserverService
{
    private ObserverRepository $repository;
    private EmotionRepository $emotionRepository;
    private ProblemService $problemService;

    public function __construct(ObserverRepository $repository, EmotionRepository $emotionRepository, ProblemService $problemService)
    {
        $this->repository = $repository;
        $this->emotionRepository = $emotionRepository;
        $this->problemService = $problemService;
    }

    /**
     * @param string $observer
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    public function enable(string $observer, QueryBuilder $queryBuilder)
    {
        /** @var Observer $observer */
        $observer::getInstance()->enable($queryBuilder);

        if($observer::getInstance() instanceof HelpObserver) {
            /** @var HelpObserver $observerService */
            $observerService = $observer::getInstance();
            $observerService->setEmotionRepository($this->emotionRepository);
            $observerService->setProblemService($this->problemService);
        }
    }

    /**
     * @param string $observer
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    public function addDependencies(Observer $observer)
    {
        /** @var Observer $observer */
        if($observer instanceof HelpObserver) {
            /** @var HelpObserver $observerService */
            $observerService = $observer::getInstance();
            $observerService->setEmotionRepository($this->emotionRepository);
            $observerService->setProblemService($this->problemService);
        }
    }

    public function getObserver(UuidInterface $observerUuid)
    {
        return $this->repository->getOneByUuid($observerUuid);
    }
}