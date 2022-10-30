<?php

namespace Jefero\Bot\Main\Domain\Common\Model;

class RedisAnswer
{
    private string $code;
    
    private string $action;
    
    private string $query;
    
    private bool $needAnswer;
    
    public function __construct(string $code, string $action, string $query, bool $needAnswer = false)
    {
        $this->code = $code;
        $this->action = $action;
        $this->query = $query;
        $this->needAnswer = $needAnswer;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function isNeedAnswer(): bool
    {
        return $this->needAnswer;
    }
}