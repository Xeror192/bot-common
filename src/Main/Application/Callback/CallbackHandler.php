<?php

namespace Jefero\Bot\Main\Application\Callback;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;
use Jefero\Bot\Main\Domain\Common\Model\RedisAnswer;
use Jefero\Bot\Main\Domain\Common\Model\RedisBagAction;
use Jefero\Bot\Main\Domain\Common\Service\CustomerRepository;
use Jefero\Bot\Main\Domain\Common\Model\Dialog;
use Jefero\Bot\Main\Domain\Common\Service\Dialog\SystemDialog;
use Jefero\Bot\Main\Domain\Common\Service\RedisBagService;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Telegram\Service\Telegram;
use Jefero\Bot\Main\Domain\VK\Service\VKClient;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryAction;
use Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction\MemoryUser;
use Ramsey\Uuid\Uuid;

class CallbackHandler
{
    const HELLO_MESSAGES = [
        "начать",
        "/start",
        "привет"
    ];

    const SYSTEM_MESSAGES = [
        "помощи",
        "помощь",
        "что ты умеешь"
    ];

    public CustomerRepository $telegramCustomerRepository;
    protected Telegram $telegram;
    protected VKClient $VKClient;
    protected CallbackCommandInterface $command;
    protected RedisBagService $redisBagService;

    protected array $dialogs = [];
    protected string $mainDialogCode;
    protected string $type;
    protected ?AbstractDialogResponseModel $responseModel = null;

    public static string $mainCode;

    public function __construct(
        CustomerRepository $telegramCustomerRepository,
        RedisBagService    $redisBagService,
        Telegram           $telegram,
        VKClient           $VKClient,
        Dialog             $mainDialog
    ) {
        $this->telegramCustomerRepository = $telegramCustomerRepository;
        $this->redisBagService = $redisBagService;
        $this->telegram = $telegram;
        $this->VKClient = $VKClient;
        $this->mainDialogCode = $mainDialog::getCode();
        self::$mainCode = $mainDialog::getCode();
        $this->dialogs = array_merge($this->dialogs, [
            $mainDialog::getCode() => $mainDialog->setCallbackHandler($this),
            SystemDialog::getCode() => (new SystemDialog())->setCallbackHandler($this)
        ]);
    }

    public function handle(CallbackCommandInterface $command, string $type): AbstractDialogResponseModel
    {
        $this->type = $type;
        $this->command = $command;

        $this->redisBagService->setType($this->type)
            ->setChatId($this->command->getChatId())
            ->setQuery($this->getMessage()->getMessage());

        if (!$this->getMessage()->getMessage() || $this->isHelloMessage($this->getMessage()->getMessage())) {
            $this->bag()->clear();
            return $this->handleAction($command);
        }

        if ($this->bag()->getBag()->action()->isGreeting()) {
            return $this->start();
        }

        if ($this->isSystemMessage($this->getMessage()->getMessage())) {
            $this->bag()->getBag()->action()->needAnswer = false;
        }

        if ($this->bag()->getBag()->action()->needAnswer) {
            return $this->handleAction($command, true);
        }

        $dialogResponse = $this->handleAction($command, true);

        if (!$this->bag()->getBag()->action()->needAnswer) {
            $this->bag()->clear();
        }

        return $dialogResponse;
    }

    public function start(): AbstractDialogResponseModel
    {
        $dialogResponse = $this->getMainDialog()->start();
        if ($dialogResponse->isNeedSendMessage() && $sender = $this->getSender()) {
            $sender->sendRawMessage($dialogResponse->getResponse($this->command->getChatId()));
        }

        return $dialogResponse;
    }

    public function clear(): void
    {
        $this->bag()->clear();
    }

    public function setParameter($name, $value): void
    {
        $this->redisBagService->setParameter($name, $value);
    }

    public function getParameter($name)
    {
        return $this->redisBagService->getParameter($name);
    }

    public function getMessage(): CallbackCommandInterface
    {
        return $this->command;
    }

    public function getSender(): Telegram|VKClient|null
    {
        if ($this->type == Telegram::CODE_TYPE) {
            return $this->telegram;
        }

        if ($this->type == VKClient::CODE_TYPE) {
            return $this->VKClient;
        }

        return null;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCustomer(string $id): ?Customer
    {
        return $this->telegramCustomerRepository->findByUsername($id);
    }

    public function getResponseModel(): ?AbstractDialogResponseModel
    {
        return $this->responseModel;
    }

    public function setResponseModel(AbstractDialogResponseModel $model): void
    {
        $this->responseModel = $model;
    }

    public function getMainDialog(): Dialog
    {
        return $this->dialogs[$this->mainDialogCode];
    }

    public function bag(): RedisBagService
    {
        return $this->redisBagService;
    }

    public function handleAction(
        CallbackCommandInterface $command,
        bool $withAction = false
    ): AbstractDialogResponseModel
    {
        $action = $this->bag()->getBag()->action();

        /** @var AbstractDialogResponseModel $dialogResponse */
        $dialogResponse = $this->dialogs[$action->code]->{$action->action}();
        if ($withAction) {
            $newAction = RedisBagAction::creatAction($action->code, $action->action, $this->getMessage()->getMessage());
            $this->bag()->getBag()->action()->add($newAction);
        }
        
        if ($dialogResponse->isNextAction()) {
            $newAction = RedisBagAction::creatAction(
                $dialogResponse->getAction()->code, 
                $dialogResponse->getAction()->action, 
                $this->getMessage()->getMessage()
            );
            
            $this->bag()->getBag()->action()->add($newAction);
            $this->bag()->save();

            $this->getResponseModel()->clearAction();
            return $this->handleAction($command);
        }

        if (!$dialogResponse->answer) {
            $answer = RedisBagAction::creatAnswer($action->code, $action->action, $this->getMessage()->getMessage());
            $dialogResponse->withAnswer($answer);
        }
        
        $this->bag()->getBag()->action()->add($dialogResponse->answer);
        $dialogResponse->answer = null;
        if ($dialogResponse && $dialogResponse->isNeedSendMessage() && $sender = $this->getSender()) {
            if ($dialogResponse->isMedia()) {
                $sender->sendMediaMessage($dialogResponse->getResponse($command->getChatId()));
                $dialogResponse->setImages([]);
            }
            $sender->sendRawMessage($dialogResponse->getResponse($command->getChatId()));
        }

        if (!$this->bag()->getBag()->action()->needAnswer) {
            $this->bag()->clear();
        }
        
        $this->bag()->save();

        return $dialogResponse;
    }

    private function isHelloMessage(string $message): bool
    {
        return in_array(mb_strtolower($message), self::HELLO_MESSAGES);
    }

    private function isSystemMessage(string $message): bool
    {
        return in_array(mb_strtolower($message), self::SYSTEM_MESSAGES);
    }
}
