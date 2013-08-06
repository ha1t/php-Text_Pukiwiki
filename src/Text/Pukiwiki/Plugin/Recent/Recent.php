<?php
/**
 * Text_PukiWiki_Plugin_Recent
 *
 */
class Text_PukiWiki_Plugin_Recent
{
    /**
     * load
     *
     * @param string $arg
     * @return string
     */
    public function load($arg)
    {
        return $this->getPageRecent($arg);
    }

    /**
     * getPageRecent
     *
     * @access public
     * @return string
     */
    function getPageRecent($prefix)
    {
        if (is_numeric($prefix)) {
            $limit = (int)$prefix;
        } else {
            $limit = 20;
        }

        $rows = PageService::getRecentPage($limit);

        foreach ($rows as $item) {
            $date = date('Y-m-d', $item['timestamp']);
            $list[$date][] = $item['pagename'];
        }

        $url = url('/');
        $link[] = '<div class="plugin_recent">';
        foreach($list as $key => $day) {
            $link[] = '<span>' . $key . '</span>';
            $link[] = '<ul>';
            foreach($day as $pagename){
                $href = str_replace('&', '&amp;', $url . $pagename);
                $link[] = '<li><a href="' . $href . '">' . $pagename . '</a></li>';
            }
            $link[] = '</ul>';
        }
        $link[] = '</div>';

        return join("\n", $link);
    }
}
