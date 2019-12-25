<?php
/**
 * vim: sw=4 :
 * CakeWiki_Plugin.php
 *
 */

require_once 'Plugin.php';

/**
 * Text_PukiWiki_CakeWikiPlugin
 *
 */
class Text_PukiWiki_CakeWikiPlugin extends Text_PukiWiki_Plugin
{
    /**
     *
     *
     */
    public function __construct($r_src)
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
    public function getPagename()
    {
        $params = Router::getParams();
        return $params['pagename'];
    }

    /**
     * makeLink
     *
     * @access protected
     */
    public function makeLink($title)
    {
        return '<a href="#k' . substr(md5($title), 0, 7) . '">' . $title . '</a>';
    }

    /**
     * getSource
     *
     * @access public
     * @param string $pagename
     */
    public function getSource($pagename = '')
    {
        if ($pagename == '') {
            return $this->src;
        } else {
            $query = "SELECT source FROM purepage WHERE pagename = ?";
            return $this->db->getOne($query, array($pagename));
        }
    }
}
