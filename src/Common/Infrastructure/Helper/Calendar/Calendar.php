<?php

namespace Jefero\Bot\Common\Infrastructure\Helper\Calendar;

class Calendar
{
    private \DateTimeImmutable $date;

    private array $days = [];

    private function __construct(?\DateTimeImmutable $date = null)
    {
        $this->date = $date ?: new \DateTimeImmutable();
    }

    /**
     * @return CalendarDay[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    public function generate(): void
    {
        $currentMonth = $this->date->format("m");
        $day = 1;
        while ($currentMonth == $this->date->modify("-$day day")->format("m")) {
            $calendarDay = new CalendarDay($this->date->modify("-$day day"), true);
            $this->days[] = $calendarDay;
            $day++;
        }

        while ($this->date->modify("-$day day")->format("N") != 7) {
            $calendarDay = new CalendarDay($this->date->modify("-$day day"), false);
            $this->days[] = $calendarDay;
            $day++;
        }

        $day = 0;

        while ($currentMonth == $this->date->modify("+$day day")->format("m")) {
            $calendarDay = new CalendarDay(
                $this->date->modify("+$day day"),
                true,
                $this->date->modify("+$day day")->getTimestamp() == $this->date->getTimestamp()
            );
            $this->days[] = $calendarDay;
            $day++;
        }

        while ($this->date->modify("+$day day")->format("N") != 1) {
            $calendarDay = new CalendarDay($this->date->modify("+$day day"), false);
            $this->days[] = $calendarDay;
            $day++;
        }

        usort($this->days, function (CalendarDay $a, CalendarDay $b) {
            return $a->date->getTimestamp() <=> $b->date->getTimestamp();
        });
    }

    public static function create(?\DateTimeImmutable $date = null): self
    {
        return new self($date);
    }
}
