<?php
/**
 * vim: sw=4 :
 * CakeWiki_Plugin.php
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */

require_once 'Plugin.php';

/**
 * Text_PukiWiki_CakeWikiPlugin
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */
class Text_PukiWiki_CakeWikiPlugin extends Text_PukiWiki_Plugin
{
    /**
     *
     *
     */
    function Text_PukiWiki_CakeWikiPlugin($r_src)
    {
        $this->Page = ClassRegistry::getObject('page');

        parent::__construct($r_src);
    }

    /**
     * getPagename
     *
     * @access protected
     * @param void
     * @return string | false
     */
    function getPagename()
    {
        $params = Router::getParams();
        return $params['pagename'];
    }

    /**
     * makeLink
     *
     * @access protected
     */
    function makeLink($title)
    {
        return '<a href="#k' . substr(md5($title), 0, 7) . '">' . $title . '</a>';
    }

    /**
     * getSource
     *
     * @access public
     * @param string $pagename
     */
    function getSource($pagename = '')
    {
        if ($pagename == '') {
            return $this->src;
        } else {
            $query = "SELECT source FROM purepage WHERE pagename = ?";
            return $this->db->getOne($query, array($pagename));
        }
    }

}
?>
