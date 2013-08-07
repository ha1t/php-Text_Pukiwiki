<?php
class Text_PukiwikiPluginTest extends PHPUnit_Framework_TestCase
{
    public function testPlugin()
    {
        $test_text = 'hoge';
        $pukiwiki = new Text_Pukiwiki(true);
        $result = $pukiwiki->toHtml($test_text);
        $this->assertEquals('<p>hoge</p>', $result);
        $this->assertGreaterThan(0, strlen($result));
    }
}

