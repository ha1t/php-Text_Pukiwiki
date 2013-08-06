<?php
class Text_PukiwikiTest extends PHPUnit_Framework_TestCase
{
    public function testParseP()
    {
        $test_text = 'hoge';
        $pukiwiki = new Text_Pukiwiki();
        $result = $pukiwiki->toHtml($test_text);
        $this->assertEquals('<p>hoge</p>', $result);
        $this->assertGreaterThan(0, strlen($result));
    }
}

