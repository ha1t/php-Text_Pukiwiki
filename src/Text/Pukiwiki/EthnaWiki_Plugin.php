<?php
/**
 * EthnaWiki_Plugin.php
 */

require_once 'Plugin.php';

/**
 * Text_PukiWiki_EthnaWikiPlugin
 *
 */
class Text_PukiWiki_EthnaWikiPlugin extends Text_PukiWiki_Plugin
{
    var $src;

    /**
     * EthnaWiki Controller
     * @var     EthnaWiki_Controller
     * @access  protected
     */
    var $ctl;

    var $db;

    /**
     * EthnaWiki Controller
     * @var     Ethna_Backend
     * @access  protected
     */
    var $backend;

    public function __construct($r_src)
    {
        $this->ctl = Ethna_Controller::getInstance();
        $this->backend = $this->ctl->getBackend();
        $this->src = $r_src;
        $this->db = $this->backend->getDB();
    }

    //{{{ getPagename
    /**
     * getPagename
     *
     * @access protected
     * @param void
     * @return string | false
     */
    function getPagename()
    {
        $url_handler = $this->ctl->getUrlHandler();
        $actionform  = $this->ctl->getActionForm();
        $pagename    = $actionform->get('pagename');

        if (is_string($pagename)) {
            return $pagename;
        }

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $tmp_vars = $_GET;
        } elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
            $tmp_vars = $_POST;
        }

        if (empty($_SERVER['URL_HANDLER']) == false) {
            $tmp_vars['__url_handler__'] = $_SERVER['URL_HANDLER'];
            $tmp_vars['__url_info__'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
            $action_info = $url_handler->requestToAction($tmp_vars);
        } else {
            trigger_error('invalid entry point', E_WARNING);
        }

        return $action_info['pagename'];
    }
    //}}}

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
