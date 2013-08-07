<?php
/**
 * Include.php
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */

require_once 'Text/PukiWiki/CakeWiki_Plugin.php';

/**
 * strlen - PukiWiki plugin
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */
class Text_PukiWiki_Plugin_Include extends Text_PukiWiki_CakeWikiPlugin
{
    /**
     * load
     *
     * @param string $arg
     */
    function load($arg)
    {
      return '';
        $src = $this->getSource($arg);
        $pukiwiki = new Text_PukiWiki();
        return $pukiwiki->toHtml($src);
    }
}
