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
class Text_PukiWiki_Plugin_List extends Text_PukiWiki_CakeWikiPlugin
{
    /**
     * load
     *
     * @access public
     */
    function load($arg)
    {
        return $this->getPageList($arg);
    }

    /**
     * getPageList
     *
     * @access public
     */
    function getPageList($prefix)
    {
        $result = $this->Page->find('all',
            array('order' => 'pagename', 'fields' => array('pagename'))
        );

        $html = '<ul>';
        foreach ($result as $row) {
            if ($row['Page']['pagename']{0} == ':') {
                continue;
            }

            // ex. halt/:himitsu
            if (strpos($row['Page']['pagename'], '/:') !== false) {
                continue;
            }

            $url = Router::url('/'.$row['Page']['pagename']);
            $html .= "<li><a href=\"{$url}\" title=\"{$row['Page']['pagename']}\">{$row['Page']['pagename']}</a></li>";
        }
        $html .= '</ul>';

        return $html;
    }
}
