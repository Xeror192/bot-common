<?php

namespace Jefero\Bot\Main\Domain\Common\Exception;

use Jefero\Bot\Common\Domain\Exception\DomainExceptionCode;

class WrongDateException extends \Exception
{
    /** @var int */
    protected $code = DomainExceptionCode::WRONG_DATE;
    /** @var string */
    protected $message = 'Неверный формат даты';
}