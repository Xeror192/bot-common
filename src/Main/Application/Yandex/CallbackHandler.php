<?php

namespace Jefero\Bot\Main\Application\Yandex;

use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\RequestAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\HelpObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Model\SystemObserver;
use Jefero\Bot\Main\Domain\Yandex\Domain\Observer\Observer;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryUser;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\Mentor\MentorService;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\Observer\ObserverService;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\RequestAction\RequestActionService;
use Jefero\Bot\Main\Domain\Yandex\Model\DialogResponseModel;
use Jefero\Bot\Common\Infrastructure\Persistence\RedisRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CallbackHandler
{
    const HELLO_MESSAGES = [
        "привет",
        "здравствуйте",
        "начать"
    ];

    const SYSTEM_MESSAGES = [
        "помощи",
        "помощь",
        "что ты умеешь"
    ];

    private MentorService $mentorService;

    private ObserverService $observerService;

    private RequestActionService $requestActionService;

    private RedisRepository $redisRepository;

    public static CallbackCommand $command; 
    
    public static MemoryAction $currentAction;
    
    public static MemoryUser $currentUser;

    private string $type = 'yandex';

    private array $observers = [
        SystemObserver::CODE => SystemObserver::class,
        HelpObserver::CODE => HelpObserver::class,
    ];

    public function __construct(MentorService $mentorService, ObserverService $observerService, RequestActionService $requestActionService, RedisRepository $redisRepository)
    {
        $this->mentorService = $mentorService;
        $this->observerService = $observerService;
        $this->requestActionService = $requestActionService;
        $this->redisRepository = $redisRepository;
    }

    public function handle(CallbackCommand $command): DialogResponseModel
    {
        self::$command = $command;

//        CallbackHandler::$currentUser = MemoryUser::create();
//        $this->saveUser();
        self::$currentUser = $this->getUser();
//        echo json_encode($this->getAction());die();
        if (!$command->getMessage()) {
            self::$currentAction = MemoryAction::createFromVoid();
            $observer = $this->getObserver(Uuid::fromString(self::$currentAction->observer))::getInstance();
            $response = $observer->continueAction(self::$currentAction);
            CallbackHandler::$currentAction->addAnswer($response->actionInfo);
            $this->saveAction(self::$currentAction);
            CallbackHandler::$currentUser = MemoryUser::create();
            $this->saveUser();
            return $response;
        }

        if (in_array($command->getMessage(), self::HELLO_MESSAGES)) {
            if ($this->getAction()) {
                self::$currentAction = MemoryAction::createWithHistory($this->getAction());
            } else {
                self::$currentAction = MemoryAction::createGreeting();
            }
            $observer = $this->getObserver(Uuid::fromString(self::$currentAction->observer))::getInstance();
            $response = $observer->continueAction(self::$currentAction);
            CallbackHandler::$currentAction->addAnswer($response->actionInfo);
            $this->saveAction(self::$currentAction);
            $this->saveUser();
            return $response;
        }

        if (!$this->getAction()) {
            return new DialogResponseModel();
        }

        self::$currentAction = MemoryAction::createFromMemory($this->getAction());

        if (in_array($command->getMessage(), self::SYSTEM_MESSAGES)) {
            self::$currentAction->needAnswer = false;
        }

        if (self::$currentAction->needAnswer) {
            $observer = $this->getObserver(Uuid::fromString(self::$currentAction->observer))::getInstance();
            $this->observerService->addDependencies($observer);

            $response = $observer->continueAction(self::$currentAction);
            CallbackHandler::$currentAction = MemoryAction::addAction(
                MemoryAction::createNew($response->actionInfo['observer'], $response->actionInfo['action'], MemoryAction::TYPE_ACTION),
                CallbackHandler::$currentAction
            );
            CallbackHandler::$currentAction->addAnswer($response->actionInfo);
            $this->saveAction(self::$currentAction);
            $this->saveUser();
            return $response;
        }
        self::$currentAction = MemoryAction::addAction($this->initializeAction(), self::$currentAction);
        $observer = $this->getObserver(Uuid::fromString(self::$currentAction->observer))::getInstance();
        $response = $observer->continueAction(self::$currentAction);
        CallbackHandler::$currentAction->addAnswer($response->actionInfo);
        $this->saveAction(self::$currentAction);
        $this->saveUser();
        return $response;
    }

    public function initializeAction(): MemoryAction
    {
        $command = self::$command;
        $queryBuilder = $this->requestActionService->getQueryForSearch(self::$command->getMessage());

        foreach ($this->observers as $observer) {
            $this->observerService->enable($observer, $queryBuilder);
        }

        /** @var RequestAction[] $actions */
        $actions = $queryBuilder->getQuery()->getResult();

        usort(
            $actions, function ($a, $b) use ($command) {
            /** @var RequestAction $a */
            /** @var RequestAction $b */
            $firstObserver = $this->getObserver($a->getObserverUuid())::getInstance();
            $secondObserver = $this->getObserver($a->getObserverUuid())::getInstance();
            $aPriority = $firstObserver->getPriority($a, $command->getMessage());
            $bPriority = $secondObserver->getPriority($b, $command->getMessage());
            if ($aPriority == $bPriority) {
                return 0;
            }
            return ($aPriority < $bPriority) ? -1 : 1;
        });

        foreach ($actions as $action) {
            $result = $this->getObserver($action->getObserverUuid())::getInstance()->action($action);

            if (!$result->isEmpty()) {
                return MemoryAction::createFromRequestAction($action);
            }
        }

        self::$currentUser = MemoryUser::create();
        return MemoryAction::createFromVoid();
    }

    /**
     * @param UuidInterface $uuid
     * @return Observer|string
     */
    public function getObserver(UuidInterface $uuid)
    {
        $observer = $this->observerService->getObserver($uuid);

        return $this->observers[$observer->getCode()];
    }

    public function removeAction()
    {
        $this->redisRepository->getRedis()->del($this->getActionCode());
    }

    public function getActionCode(): string
    {
        return $this->type . "." . self::$command->getChatId() . "." . "action";
    }

    public function getUserCode(): string
    {
        return $this->type . "." . self::$command->getChatId() . "." . "user";
    }

    public function getAction()
    {
        $action = $this->redisRepository->getRedis()->get($this->getActionCode());
        if ($action) {
            return json_decode($action, true);
        }

        return null;
    }

    public function getUser(): ?MemoryUser
    {
        $user = $this->redisRepository->getRedis()->get($this->getUserCode());
        if ($user) {
            return MemoryUser::fromArray(json_decode($user, true));
        }

        return MemoryUser::create();
    }

    public function saveAction(MemoryAction $action)
    {
        $this->redisRepository->getRedis()->set($this->getActionCode(), json_encode($action->toArray()));
    }

    public function saveUser()
    {
        $this->redisRepository->getRedis()->set($this->getUserCode(), json_encode(self::$currentUser->toArray()));
    }
}