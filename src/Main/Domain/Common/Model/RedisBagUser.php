<?php

namespace Jefero\Bot\Main\Domain\Common\Model;

class RedisBagUser
{
    private string $userCode;
    private array $params = [];
    
    public static function create(string $userCode): self
    {
        $user = new self();
        $user->userCode = $userCode;
        
        return $user;
    }
    
    public static function createFromMemory(array $object): self
    {
        $user = new self();
        $user->userCode = $object['userCode'];
        
        return $user;
    }
    
    public function toArray(): array
    {
        return [
            'userCode' => $this->userCode
        ];
    }
}