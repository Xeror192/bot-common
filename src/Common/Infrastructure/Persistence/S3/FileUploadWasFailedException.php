<?php

namespace Jefero\Bot\Common\Infrastructure\Persistence\S3;

class FileUploadWasFailedException extends \Exception
{
    /** @var string */
    protected $message = 'File upload was failed';
}
