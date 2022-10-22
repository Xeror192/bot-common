<?php

namespace App\Tests;

use Codeception\PHPUnit\TestCase;

class LessonTestCase extends TestCase
{
    public ?string $result = null;

    protected function getWorkFile(): string
    {
        return $_ENV['PATH_TO_PROJECT'];
    }

    protected function assertIsClassExist(string $className)
    {
        $this->assertTrue(class_exists($className));
    }

    protected function assertResultEquals($expected, ?string $message = 'Вывод кода не равен ожидаемому результату')
    {
        $this->assertEquals($expected, $this->result, 'Error_' . $message);
    }
}