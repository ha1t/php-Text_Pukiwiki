<?php
/**
 * Image.php
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @package EthnaWiki
 */

require_once 'Text/PukiWiki/EthnaWiki_Plugin.php';

/**
 * Text_PukiWiki_Plugin_Image
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @package EthnaWiki
 */
class Text_PukiWiki_Plugin_Image extends Text_PukiWiki_EthnaWikiPlugin
{
    var $type = array(
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'swf' => 'application/x-shockwave-flash'
    );
    
    /**
     * load
     *
     * @access public
     * @param string $arg
     */
    function load($arg)
    {
        return $this->getImage($arg);
    }

    function loadFromUrl()
    {
        return $this->printImage();
    }

    /**
     * printImage
     *
     * @access public
     */
    function printImage()
    {
        $db = $this->backend->getDB();
        $af = $this->backend->getActionForm();

        $query = explode('/', $af->get('query'));

        $filename = array_pop($query);
        $pagename = implode('/', $query);

        if(empty($filename) || empty($pagename)){
            exit();
        }

        //check ext
        if(!mb_ereg('\.(.+?)$', $filename, $matches) || empty($this->type[$matches[1]])){
            exit();
        }
        
        $query  = "SELECT binary FROM attach";
        $query .= " WHERE pagename = ? AND filename = ?;";
        $binary = $db->getOne($query, array($pagename, $filename));
        
        if ($binary === false) {
            exit();
        }

        header('Content-Type: ' . $this->type[$matches[1]]);
        header('Content-Length: ' . strlen($binary));
        print($binary);

        exit();
    }

    /**
     * getImage
     *
     * @access public
     * @param string $filename
     */
    function getImage($filename)
    {
        $pagename = $this->getPagename();

        $config = $this->backend->getConfig();
        $url = $config->get('url') . basename($_SERVER['SCRIPT_NAME']) . '/';

        $img_url = $url . 'file/?pagename=' . $pagename . '&amp;filename=' . $filename;

        $link  = '<a href="' . $img_url . '">';
        $link .= '<img src="' . $img_url . '" />';
        $link .= '</a>';

        return $link;
    }
}
