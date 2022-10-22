<?php

namespace Jefero\Bot\Main\Domain\Common\Service;

use Jefero\Bot\Common\Infrastructure\Persistence\RedisRepository;

class RedisBagService
{
    private RedisRepository $redisRepository;

    private ?string $type;

    private ?string $chatId;

    public function __construct(RedisRepository $redisRepository)
    {
        $this->redisRepository = $redisRepository;
    }

    public function setType(?string $type): RedisBagService
    {
        $this->type = $type;
        return $this;
    }

    public function setChatId(?string $chatId): RedisBagService
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function getActionCode(): string
    {
        return "{$this->type}.{$this->chatId}.action";
    }

    public function getAttachmentCode(): string
    {
        return "{$this->type}.{$this->chatId}.attachment";
    }

    public function getAttachments()
    {
        $attachment = $this->redisRepository->getRedis()->get($this->getAttachmentCode());
        if ($attachment) {
            return json_decode($attachment, true);
        }

        return null;
    }

    public function addAttachment($attachment): void
    {
        $attachments = $this->redisRepository->getRedis()->get($this->getAttachmentCode());
        if ($attachments) {
            $attachments = json_decode($attachments, true);
        } else {
            $attachments = [];
        }

        $attachments[] = $attachment;
        $this->redisRepository->getRedis()->set($this->getAttachmentCode(), json_encode($attachments));
    }

    public function removeAttachments(): void
    {
        $this->redisRepository->getRedis()->del($this->getAttachmentCode());
    }

    public function getAction()
    {
        $action = $this->redisRepository->getRedis()->get($this->getActionCode());
        if ($action) {
            return json_decode($action, true);
        }

        return null;
    }

    public function removeAction(): void
    {
        $this->redisRepository->getRedis()->del($this->getActionCode());
    }

    public function setAction($action): void
    {
        $this->redisRepository->getRedis()->set($this->getActionCode(), json_encode($action));
    }

    public function setParameter($name, $value): void
    {
        $action = $this->getAction();

        if (!$action) {
            $action = [];
        }

        $action[$name] = $value;

        $this->setAction($action);
    }

    public function getParameter($name)
    {
        $action = $this->getAction();

        if (!$action) {
            return null;
        }

        return $action[$name] ?? null;
    }
}