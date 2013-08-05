<?php
class Text_PukiwikiTest extends PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $pukiwiki = new Text_Pukiwiki();
        $result = $pukiwiki->toHtml('hoge');
        $this->assertGreaterThan(0, strlen($result));
    }
}

