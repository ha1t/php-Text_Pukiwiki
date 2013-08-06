<?php
/**
 * Plugin.php
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */

/**
 * Text_PukiWiki_Plugin
 *
 * @package Text_PukiWiki
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */
class Text_PukiWiki_Plugin
{
    var $src;

    /**
     * Text_PukiWiki_Plugin
     *
     */
    function Text_PukiWiki_Plugin($r_src)
    {
        $this->src = $r_src;
    }

    /**
     * getSource
     *
     */
    function getSource()
    {
        return $this->src;
    }
}
?>
