<?php
/**
 * vim: sw=4
 * Rss.php
 *
 */

require_once 'Text/PukiWiki/CakeWiki_Plugin.php';

/**
 * Text_PukiWiki_Plugin_Rss
 *
 */
class Text_PukiWiki_Plugin_Rss extends Text_PukiWiki_CakeWikiPlugin
{
    /**
     * load
     *
     * @access public
     */
    function load($arg)
    {
        return $this->getPageRss($arg);
    }

    /**
     * getPageRss
     *
     * @access public
     */
    function getPageRss($url)
    {
        App::import('Core', 'HttpSocket');
        $http_socket = new HttpSocket();
        $rss = $http_socket->get($url);

        require_once 'Keires/Feed.php';

        $html = '';

        try {
            $feed = new Keires_Feed($url);
            $feed->parse();
            $items = $feed->getItems();

            $html = '<ul>';
            foreach ($items as $item) {
                if (!isset($item['link']) && isset($item['url'])) {
                    $item['link'] = $item['url'];
                }
                $html .= "<li><a href=\"{$item['link']}\">{$item['title']}</a></li>";
            }
            $html .= '</ul>';
        } catch (Exception $e) {
            $html = $e->getMessage();
        }

        return $html;
    }
}
