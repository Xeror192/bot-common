<?php

namespace Jefero\Bot\Common\Domain\Entity;

interface FileInfo
{
    public static function getBucket(): string;

    public function getPath(): string;
}
