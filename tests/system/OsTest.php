<?php

    namespace system;

    use mark\system\Os;
    use PHPUnit\Framework\TestCase;

    class OsTest extends TestCase
    {
        public function testGetAgent(){
            Os::getAgent();
        }

    }
