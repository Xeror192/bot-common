<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\RequestAction;

class MemoryUser
{
    public ?string $problem = '';

    public ?string $problemCode = '';

    /** @var Emotion[] */
    public array $emotions = [];

    public static function create(): self
    {
        return new self();
    }

    public function toArray(): array
    {
        $response = [
            'problem' => $this->problem,
            'problemCode' => $this->problemCode,
            'emotions' => []
        ];

        foreach ($this->emotions as $emotion) {
            $response['emotions'][] = $emotion->toArray();
        }

        return $response;
    }

    public static function fromArray(array $object): self
    {
        $response = new self();
        $response->problem = $object['problem'];
        $response->problemCode = $object['problemCode'];

        foreach ($object['emotions'] as $emotion) {
            $response->emotions[] = Emotion::createFromMemory($emotion);
        }
        return $response;
    }
}