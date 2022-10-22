<?php

namespace Jefero\Bot\Common\Infrastructure\Helper\Calendar;

class CalendarDay
{
    public \DateTimeImmutable $date;

    public bool $isCurrentMonth = true;

    public bool $isCurrentDay = false;

    public function __construct(\DateTimeImmutable $date, bool $isCurrentMonth, bool $isCurrentDay = false)
    {
        $this->date = $date;
        $this->isCurrentMonth = $isCurrentMonth;
        $this->isCurrentDay = $isCurrentDay;
    }

    public function getDay(): string
    {
        return $this->date->format("d");
    }
}
