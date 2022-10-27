<?php

namespace Jefero\Bot\Main\Application\Callback;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;
use Jefero\Bot\Main\Domain\Common\Service\CustomerRepository;
use Jefero\Bot\Main\Domain\Common\Model\Dialog;
use Jefero\Bot\Main\Domain\Common\Service\RedisBagService;
use Jefero\Bot\Main\Domain\Message\Model\AbstractDialogResponseModel;
use Jefero\Bot\Main\Domain\Telegram\Service\Telegram;
use Jefero\Bot\Main\Domain\VK\Service\VKClient;
use Jefero\Bot\Common\Infrastructure\Persistence\DoctrineRepository;

class CallbackHandler
{
    const HELLO_MESSAGES = [
        "Начать",
        "/start",
        "Привет"
    ];

    public CustomerRepository $telegramCustomerRepository;
    protected Telegram $telegram;
    protected VKClient $VKClient;
    protected CallbackCommandInterface $command;
    protected RedisBagService $redisBagService;
    public DoctrineRepository $doctrineRepository;

    protected array $dialogs = [];
    protected string $mainDialogCode;
    protected string $type;

    protected ?AbstractDialogResponseModel $responseModel = null;

    public function __construct(
        DoctrineRepository       $doctrineRepository,
        CustomerRepository       $telegramCustomerRepository,
        RedisBagService          $redisBagService,
        Telegram                 $telegram,
        VKClient                 $VKClient,
        Dialog               $mainDialog
    ) {
        $this->telegramCustomerRepository = $telegramCustomerRepository;
        $this->redisBagService = $redisBagService;
        $this->doctrineRepository = $doctrineRepository;
        $this->telegram = $telegram;
        $this->VKClient = $VKClient;
        $this->mainDialogCode = $mainDialog::getCode();
        $this->dialogs = array_merge($this->dialogs, [
            $mainDialog::getCode() => $mainDialog->setCallbackHandler($this)
        ]);
    }

    public function handle(CallbackCommandInterface $command, string $type): AbstractDialogResponseModel
    {
        $this->type = $type;
        $this->command = $command;

        $this->redisBagService->setType($this->type)->setChatId($this->command->getChatId());

        if (in_array($command->getMessage(), self::HELLO_MESSAGES)) {
            $this->clear();
        }

        if (!empty($this->command->getPhoto())) {
            $this->addAttachment($this->command->getPhoto());
        }

        $action = $this->getAction();

        if (!$action) {
            return $this->start();
        }

        /** @var AbstractDialogResponseModel $dialogResponse */
        $dialogResponse = $this->dialogs[$action["code"]]->{$action["action"]}();

        if ($dialogResponse && $sender = $this->getSender()) {
            if ($dialogResponse->isMedia()) {
                $sender->sendMediaMessage($dialogResponse->getResponse($command->getChatId()));
                $dialogResponse->setImages([]);
            }
            $sender->sendRawMessage($dialogResponse->getResponse($command->getChatId()));
        }

        return $dialogResponse;
    }

    public function start(): AbstractDialogResponseModel
    {
        $dialog = $this->getMainDialog()->start();

        if ($sender = $this->getSender()) {
            $sender->sendRawMessage($dialog->getResponse($this->command->getChatId()));
        }

        return $dialog;
    }

    public function getAttachments()
    {
        return $this->redisBagService->getAttachments();
    }

    public function addAttachment($attachment): void
    {
        $this->redisBagService->addAttachment($attachment);
    }

    public function getAction()
    {
        return $this->redisBagService->getAction();
    }

    public function clear(): void
    {
        $this->redisBagService->removeAttachments();
        $this->redisBagService->removeAction();
    }

    public function setAction($action): void
    {
        $this->redisBagService->setAction($action);
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

    public function getMainDialog()
    {
        return $this->dialogs[$this->mainDialogCode];
    }
}
