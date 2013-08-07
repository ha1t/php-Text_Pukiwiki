<?php
/**
 * Attachfilelist.php
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @package EthnaWiki
 */

require_once 'Text/PukiWiki/EthnaWiki_Plugin.php';

/**
 * Text_PukiWiki_Plugin_AttachFileList
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @package EthnaWiki
 */
class Text_PukiWiki_Plugin_Attachfilelist extends Text_PukiWiki_EthnaWikiPlugin
{
    /**
     * load
     *
     * @access public
     * @param string $arg
     */
    function load($arg)
    {
        return $this->getAttachFileList($arg);
    }

    /**
     * getPageAttachFileList
     *
     * @access public
     */
    function getAttachFileList($prefix)
    {
        $db = $this->backend->getDB();
        $pagename = $this->getPagename();
        $config = $this->backend->getConfig();
        $url = $config->get('base_url');

        $query  = "SELECT filename FROM attach WHERE pagename = ?";
        $query .= " ORDER BY filename ASC";
        $list = $db->getCol($query, array($pagename));

        if (count($list) == 0) {
            return "<ul><li>添付ファイルはありません</li></ul>";
        }

        foreach($list as $filename){
            $li = '<li>';
            $li.= "<a href=\"{$url}/file/?pagename={$pagename}&amp;filename={$filename}\">{$filename}</a>";
            $li.= '</li>';
            $link[] = $li;
        }

        return "<ul>\n" . join("\n", $link) . "\n</ul>\n";
    }
}
