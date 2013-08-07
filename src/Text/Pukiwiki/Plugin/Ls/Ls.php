<?php
/**
 * Ls plugin
 *
 */

/**
 * Text_PukiWiki_Plugin_Ls
 *
 */
class Text_PukiWiki_Plugin_Ls
{
    /**
     * load
     */
    public function load($arg)
    {
        return $this->getList($arg);
    }

    /**
     * getList
     *
     * @return string
     */
    private function getList($prefix)
    {
        $pagename = Param::get('pagename');

        if (empty($prefix)) {
            $prefix = $pagename;
        }

        $page_list = PageService::search($pagename);

        $url = url('/');
        $html = '<ul>';
        foreach ($page_list as $page) {
          $html .= "<li><a href=\"{$url}{$page->pagename}\" title=\"{$page->pagename}\">{$page->pagename}</a></li>";
        }
        $html .= '</ul>';

        return $html;
    }
}
