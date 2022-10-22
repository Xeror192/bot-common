<?php

namespace Jefero\Bot\Main\Domain\Message\Model;

abstract class AbstractDialogResponseModel
{
    public string $text = "";

    public array $buttons = [];

    public array $images = [];

    public static function mock(): self
    {
        return new static();
    }

    abstract public function reload($text, $buttons = [], $params = []): self;

    abstract public function setImages(array $images): self;

    public static function create($text, $buttons = []): self
    {
        $response = new static();
        $response->text = $text;
        $response->buttons = $buttons;
        $response->images = [];
        return $response;
    }

    abstract public function getResponse(string $chatId): array;

    public function isEmpty(): bool
    {
        return !$this->text && empty($this->buttons) && empty($this->images);
    }

    public function isMedia(): bool
    {
        return !empty($this->images);
    }
}
