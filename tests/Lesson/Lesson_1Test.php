<?php

namespace App\Tests\Lesson;

use App\Tests\LessonTestCase;

class Lesson_1Test extends LessonTestCase
{
    public function testSuccess()
    {
        ob_start();
        print eval('?>'. file_get_contents($this->getWorkFile()));
        $this->result = ob_get_contents();
        ob_end_clean();

//        $this->assertResultEquals('Hello World');
        $this->assertTrue(!empty($a));
        $this->assertTrue(!empty($a) && $a == 10);
//        $this->assertIsClassExist('User');
    }
}