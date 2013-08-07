<?php
class Text_PukiWiki_Plugin_Contents implements Plugin
{
    /**
     * load
     *
     * @param string $arg
     * @return string
     */
    public function load($arg)
    {
        $source = Page::get(Param::get('pagename'))->source;
        return $this->parseSource($source);
    }

    /**
     * parseSource
     *
     * @param string $src
     * @return string
     */
    private function parseSource($src)
    {
        $match = "";
        preg_match_all('/\n(\*{1,5})(.*?)\n/', $src, $match, PREG_SET_ORDER);

        return $this->parseList($match);
    }

    /**
     * parseList
     *
     * @param array $params
     * @param int $current_level
     * @return string
     */
    private function parseList(&$params, $current_level = 1)
    {
        $list = array();
        $list[] = "\n" . str_repeat(" ", $current_level * 2) . "<ul>\n";
        while(!is_null($param = array_shift($params))) {
            $level = strlen($param[1]);
            $value = trim($param[2]);

            if ($current_level < $level) {
                array_pop($list);
                array_unshift($params, $param);
                $list[] = $this->parseList($params, $level) . str_repeat(" ", $current_level * 2) . "</li>\n";
            } else if ($level < $current_level) {
                array_unshift($params, $param);
                break;
            } else {
                $value = $this->makeLink($value);
                $list[] = str_repeat(" ", $level * 2) . "<li>{$value}";
                $list[] = "</li>\n";
            }

        }
        $list[] = str_repeat(" ", $current_level * 2) . "</ul>\n";

        return implode('', $list);
    }

    /**
     * makeLink
     *
     * @access protected
     */
    private function makeLink($title)
    {
        return '<a href="#k' . substr(md5($title), 0, 7) . '">' . $title . '</a>';
    }
}
