<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction;

class Emotion
{
    public ?string $code;
    public ?string $name;
    public int $score;
    public int $factor;

    public static function createFromMemory(array $object): self
    {
        $response = new self();
        $response->code = $object['code'];
        $response->name = $object['name'];
        $response->score = $object['score'];
        $response->factor = $object['factor'];

        return $response;
    }

    public static function createFromEntity(\App\Bot\Domain\Yandex\Domain\Entity\Emotion $emotion): self
    {
        $response = new self();
        $response->code = $emotion->getCode();
        $response->name = $emotion->getName();
        $response->score = 0;
        $response->factor = $emotion->getScore();

        return $response;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'factor' => $this->factor,
            'score' => $this->score,
        ];
    }
}