<?php

namespace Jefero\Bot\Common\Infrastructure\Helper;

class DateHelper
{
    private \DateTimeImmutable $date;

    private function __construct()
    {
    }

    public static function createFromString(string $date): self
    {
        $dateHelper = new self();
        try {
            $dateHelper->date = new \DateTimeImmutable($date);
        } catch (\Exception $e) {
            $dateHelper->date = new \DateTimeImmutable();
        }

        return $dateHelper;
    }

    public static function createFromDate(\DateTimeImmutable $dateTimeImmutable): self
    {
        $dateHelper = new self();

        $dateHelper->date = $dateTimeImmutable;
        return $dateHelper;
    }

    public function getFormattedDate(): string
    {
        $currentDate = new \DateTimeImmutable();
        $diff = $currentDate->diff($this->date);
        $daySeconds = $diff->y * 365.25 + $diff->m * 30 + $diff->d;
        $diffInSeconds = (int)(($daySeconds * 24 + $diff->h) * 60 + $diff->i) * 60 + $diff->s;

        if ($diffInSeconds < 3600) {
            return $this->formatMinute($diffInSeconds);
        } elseif ($diffInSeconds < 86400) {
            return $this->formatHour($diffInSeconds);
        } elseif ($diffInSeconds < 259200) {
            return $this->formatDay($diffInSeconds);
        }

        return $this->formatDate($this->date);
    }

    public static function getDateDiffInSeconds(\DateTimeImmutable $firstDate, \DateTimeImmutable $secondDate)
    {
        $diff = $secondDate->diff($firstDate);
        $daySeconds = $diff->y * 365.25 + $diff->m * 30 + $diff->d;
        $diffInSeconds = (int)(($daySeconds * 24 + $diff->h) * 60 + $diff->i) * 60 + $diff->s;

        return $diffInSeconds;
    }

    public function getFormattedThrowDate(): string
    {
        $currentDate = (new \DateTimeImmutable())->modify("+3 hours");
        $diff = $currentDate->diff($this->date);
        $daySeconds = $diff->y * 365.25 + $diff->m * 30 + $diff->d;
        $diffInSeconds = (int)(($daySeconds * 24 + $diff->h) * 60 + $diff->i) * 60 + $diff->s;

        if ($diffInSeconds < 3600) {
            return $this->formatThrowMinute($diffInSeconds);
        } elseif ($diffInSeconds < 86400) {
            return $this->formatThrowHour($diffInSeconds);
        } elseif ($diffInSeconds < 259200) {
            return $this->formatThrowDay($diffInSeconds);
        }

        return $this->formatDate($this->date);
    }

    private function formatMinute(int $diffInSeconds): string
    {
        $status = ["а", "ы", ""];
        $value = $diffInSeconds / 60;
        $array = [2, 0, 1, 1, 1, 2];
        return $value . " минут" . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]] . " назад.";
    }

    private function formatHour(int $diffInSeconds): string
    {
        $status = ["", "а", "ов"];
        $value = ceil($diffInSeconds / 3600);
        $array = [2, 0, 1, 1, 1, 2];
        return $value . " час" . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]] . " назад.";
    }

    private function formatDay(int $diffInSeconds): string
    {
        $status = ["день", "дня", "дней"];
        $value = ceil($diffInSeconds / 86400);
        $array = [2, 0, 1, 1, 1, 2];
        return "через " . $value . " " . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]];
    }

    private function formatThrowMinute(int $diffInSeconds): string
    {
        $status = ["а", "ы", ""];
        $value = round($diffInSeconds / 60);
        $array = [2, 0, 1, 1, 1, 2];
        return "через " . $value . " минут" . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]];
    }

    private function formatThrowHour(int $diffInSeconds): string
    {
        $status = ["", "а", "ов"];
        $value = floor($diffInSeconds / 3600);
        $array = [2, 0, 1, 1, 1, 2];
        return "через " . $value . " час" . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]];
    }

    private function formatThrowDay(int $diffInSeconds): string
    {
        $status = ["день", "дня", "дней"];
        $value = ceil($diffInSeconds / 86400);
        $array = [2, 0, 1, 1, 1, 2];
        return $value . " " . $status[($value % 100 > 4 && $value % 100 < 20)
                ? 2
                : $array[($value % 10 < 5) ? $value % 10 : 5]] . " назад.";
    }

    private function formatDate(\DateTimeImmutable $date): string
    {
        $months = [
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "декабря",
        ];

        return $date->format("d") . " " . $months[(int)$date->format("m") - 1] . " " . $date->format("Y") . " года.";
    }
}
