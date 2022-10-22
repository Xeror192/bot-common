<?php

namespace Jefero\Bot\Main\Application\Callback;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;
use Jefero\Bot\Main\Domain\Common\Service\CustomerRepository;
use Jefero\Bot\Main\Domain\Common\Service\Dialog\MainDialog;
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
    private Telegram $telegram;
    private VKClient $VKClient;
    private CallbackCommandInterface $command;
    private RedisBagService $redisBagService;
    public DoctrineRepository $doctrineRepository;

    protected array $dialogs = [];
    private string $type;

    private ?AbstractDialogResponseModel $responseModel = null;

    public function __construct(
        DoctrineRepository       $doctrineRepository,
        CustomerRepository       $telegramCustomerRepository,
        RedisBagService          $redisBagService,
        Telegram                 $telegram,
        VKClient                 $VKClient,
        MainDialog               $mainDialog
    ) {
        $this->telegramCustomerRepository = $telegramCustomerRepository;
        $this->redisBagService = $redisBagService;
        $this->doctrineRepository = $doctrineRepository;
        $this->telegram = $telegram;
        $this->VKClient = $VKClient;
        $this->dialogs = array_merge($this->dialogs, [
            MainDialog::CODE => $mainDialog->setCallbackHandler($this)
        ]);
    }

    /**
     * @param CallbackCommandInterface $command
     * @param string $type
     */
    public function handle(CallbackCommandInterface $command, string $type): void
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
            $this->start();
            return;
        }

        /** @var AbstractDialogResponseModel $dialogResponse */
        $dialogResponse = $this->dialogs[$action["code"]]->{$action["action"]}();

        if ($dialogResponse) {
            if ($dialogResponse->isMedia()) {
                $this->getSender()->sendMediaMessage($dialogResponse->getResponse($command->getChatId()));
                $dialogResponse->setImages([]);
            }
            $this->getSender()->sendRawMessage($dialogResponse->getResponse($command->getChatId()));
        }
    }

    public function start(): void
    {
        $this->getSender()->sendRawMessage($this->getMainDialog()->start()->getResponse($this->command->getChatId()));
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
        return $this->dialogs[MainDialog::getCode()];
    }
}
