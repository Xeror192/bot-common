<?php

namespace Jefero\Bot\Common\Infrastructure\Persistence\S3;

class FileNotFoundException extends \Exception
{
    /** @var string */
    protected $message = 'File not found';
}
