<?php

namespace App\Main\Domain\Common\Model;

use Jefero\Bot\Main\Domain\Common\Entity\Customer;

class RedisBag
{
    private RedisBagAction $action;

    private RedisBagUser $user;

    private Customer $customer;
}