<?php
/**
 * Fixme.php
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */

require_once 'Text/PukiWiki/Plugin.php';

/**
 * fixme - PukiWiki plugin
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */
class Text_PukiWiki_Plugin_Fixme extends Text_PukiWiki_Plugin
{
    var $version = "0.0.1";

    function load($arg)
    {
        if ($arg == "LIST") {
            return $this->getFixmeList($this->getSource());
        } else {
            $id = rawurlencode($arg);
            return "<strong><a id=\"fixme_{$id}\">FIXME:{$arg}</a></strong>";
        }
    }

    function getFixmeList($src)
    {
        $match = "";
        preg_match_all('/#fixme\((.*?)\)/', $src, $match, PREG_PATTERN_ORDER);
        $match[1];
        $result = array();
        $result[] = "<ul>";
        foreach ($match[1] as $value) {
            if ($value == 'LIST') {
                continue;
            } else {
                $href = urlencode($value);
                $result[] = "<li><a href=\"#fixme_{$href}\">{$value}</a></li>\n";
            }
        }
        $result[] = "</ul>";

        return implode('', $result);
    }

}
