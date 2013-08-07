<?php
/**
 * Newpage.php
 *
 */

require_once 'Text/PukiWiki/CakeWiki_Plugin.php';

/**
 * Text_PukiWiki_Plugin_Newpage
 *
 */
class Text_PukiWiki_Plugin_Newpage extends Text_PukiWiki_CakeWikiPlugin
{
    /**
     * load
     *
     * @access public
     */
    function load($arg)
    {
        return $this->drawForm($arg);
    }

    /**
     * getPageNewpage
     *
     * @access public
     */
    function drawForm($prefix)
    {
        $url = Router::url('/');
        $html = <<<EOD
<form method="get" action="{$url}">
<input type="hidden" name="mode" value="edit" />
<input type="text" name="pagename" value="" />
<input type="submit" value="新規ページ作成" />
</form>
EOD;
        return $html;
    }
}
