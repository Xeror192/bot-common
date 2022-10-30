<?php

namespace Jefero\Bot\Main\Domain\Common\Model;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;

class RedisBag
{
    private RedisBagAction $action;

    private RedisBagUser $user;

    private ?Customer $customer = null;
    
    public function toArray(): array
    {
        return [
            'action' => $this->action->toArray(),  
            'user' => $this->user->toArray(),  
            'customer' => $this->customer ? $this->customer->getUuid() : null,  
        ];
    }
    
    public static function createFromVoid(string $chatdId, string $query): self
    {
        $bag = new self();
        $bag->action = RedisBagAction::createFromVoid($query);
        $bag->user = RedisBagUser::create($chatdId);
        return $bag;
    }
    
    public static function createFromMemory(array $action, array $user, ?Customer $customer = null): self
    {
        $bag = new self();
        $bag->action = RedisBagAction::createFromMemory($action);
        $bag->user = RedisBagUser::createFromMemory($user);
        $bag->customer = $customer;
        
        return $bag;
    }
    
    public function action(): RedisBagAction
    {
        return $this->action;
    }
}