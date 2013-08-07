<?php
/**
 * vim: sw=4
 * List.php
 *
 */

require_once 'Text/PukiWiki/CakeWiki_Plugin.php';

/**
 * Text_PukiWiki_Plugin_List
 *
 */
class Text_PukiWiki_Plugin_Pagecount extends Text_PukiWiki_CakeWikiPlugin
{
    /**
     * load
     *
     * @access public
     */
    function load($arg)
    {
        return $this->Page->find('count');
    }
}
