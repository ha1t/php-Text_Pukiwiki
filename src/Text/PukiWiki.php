<?php
/**
 * PukiWikiParser
 *
 * @category Text
 * @package Text_PukiWiki
 */

ini_set('include_path', ini_get('include_path') . ':' . dirname(dirname(__FILE__)));

/**
 * PukiWikiParser
 *
 * @package Text_PukiWiki
 */
class Text_PukiWiki
{
    const VERSION = "0.1.2";

    /**
     * $use_plugin
     * @var     bool
     * @access  private
     */
    var $use_plugin = true;

    /**
     * $h_start_level
     * @var     int
     * @access  protected
     */
    var $h_start_level = 3;

    var $linkwords = array();
    var $base_url = "";

    /**
     * Text_PukiWiki
     *
     * @access public
     */
    function Text_PukiWiki()
    {
        if ($this->use_plugin) {
            include_once dirname(__FILE__) . '/PukiWiki/PluginHandler.php';
        }
    }

    function setLinkWords($list)
    {
        if (is_array($list)) {
            $this->linkwords = $list;
        }
    }

    function setBaseUrl($url)
    {
        $this->base_url = $url;
    }

    //{{{ toHtml
    /**
     * toHtml
     *
     * @access public
     * @param string $src
     */
    function toHtml($src)
    {
        $buf = array();
        $lines = explode("\n", rtrim($src));
        array_walk($lines, 'rtrim');

        while(current($lines) !== false) {

            $line = current($lines);

            switch(true) {

                case ($line == ''):
                    array_shift($lines);
                    $buf[] = '';
                    break;
                case preg_match("|^//|", $line):
                    array_shift($lines);
                    $buf[] = "";
                    break;
                case preg_match("/^----/", $line):
                    array_shift($lines);
                    $buf[] = "<hr />";
                    break;
                case preg_match("/^\*/", $line):
                    $buf[] = $this->parseH(array_shift($lines));
                    break;
                case preg_match("/^\#/", $line):
                    if ($this->use_plugin) {
                        $buf[] = $this->parsePlugin(array_shift($lines), $src);
                        break;
                    }
                case preg_match("/\\A\\s/", $line):
                    $buf[] = $this->parsePre($lines);
                    break;
                case preg_match("/^>/", $line):
                    $buf[] = $this->parseQuote($lines);
                    break;
                case preg_match("/^\-/", $line):
                    $buf[] = $this->parseList('ul', $lines, "/^\-/");
                    break;
                case preg_match("/^\+/", $line):
                    $buf[] = $this->parseList('ol', $lines, "/^\+/");
                    break;
                case preg_match("/\\A:/", $line):
                    $buf[] = $this->parseDl($lines, "/^\:/");
                    break;
                case preg_match('_^\|_', $line):
                    $buf[] = $this->parseTable($lines, '_^\|_');
                    break;
                default:
                    $buf[] = $this->parseP($lines, '/\A(?![\*\s>:\-\+]|----|\z)/');

            }

        }

        $html = implode("\n", $buf);

        if (extension_loaded('mecab')) {
          $mecab = new Mecab_Tagger(array('-d', APP . 'vendors/mecab_dic'));
          $buf = $this->escapeLink($html);
          $source_conv = $mecab->parse($buf['text']);
          $html = $this->unescapeLink($source_conv, $buf['replace_list']);
        }

        return $html;
    }
    //}}}

    /**
     * takeBlock
     *
     * @access private
     */
    function takeBlock(&$lines, $regexp)
    {
        $buf = array();
        
        while($line = current($lines)) {
            
            if (preg_match($regexp, $line)) {
                $buf[] = preg_replace($regexp, '', array_shift($lines), 1);
             } else {
                break;
            }

        }

        return implode("\n", $buf);
    }

    /**
     * parseTable
     *
     * @access private
     */
    function parseTable(&$lines, $regexp)
    {
        $block = explode("\n", $this->takeBlock($lines, $regexp));
        $table = "<table>\n";

        foreach ($block as $key => $line) {
            $colums = explode("|", $line);
            foreach ($colums as $key => $value) {
                $colums[$key] = "  <td>" . trim($this->parseInline($value)) . "</td>";
            }
            $table .= "<tr>" . implode("\n", $colums) . "</tr>\n";
        }

        $table .= "</table>";

        return $table;
    }

    function parseP(&$lines, $regexp)
    {
        $value = explode("\n", $this->takeBlock($lines, $regexp));
        
        foreach ($value as $key => $line) {
            $value[$key] = $this->parseInline($line);
        }

        return "<p>" . implode("\n", $value) . "</p>";
    }

    /**
     * parseInline
     *
     * @access protected
     * @param string $line
     * @param array  $word_list
     */
    function parseInline($line)
    {
        $line = htmlspecialchars($line, ENT_QUOTES);

        //collon link
        $line = preg_replace("/\[\[(.+?):\s*?(https?:\/\/\S+?)\s*?\]\]/",
            '<a href="$2">$1</a>',
            $line
        );

        //gt link
        $line = preg_replace("|\[\[(.+?)\&gt;\s*?(https?:\/\/\S+?)\s*?\]\]|",
            '<a href="$2">$1</a>',
            $line
        );

        //internal link
        if (count($this->linkwords) > 0) {
            $line = $this->makeInternalLink($line);
        }

        return $this->autoLink($line);
    }

    private function makeInternalLink($line)
    {
        $re = preg_match_all("|\[\[(.+?)\&gt;\s*?(.+?)\s*?\]\]|", $line, $matches, PREG_SET_ORDER);

        if ($re != 0) {
            foreach ($matches as $match) {
                if (in_array($match[2], $this->linkwords)) {
                    $replace_to = '<a class="internal" href="';
                    $replace_to.= "{$this->base_url}/{$match[2]}\">{$match[1]}</a>";
                } else {
                    $replace_to = '<a class="internal" href="';
                    $replace_to.= "{$this->base_url}/?mode=edit&amp;pagename={$match[2]}\">{$match[1]}</a>";
                }
                $line = str_replace($match[0], $replace_to, $line);
            }
        }

        $pattern = "|\[\[(.+?)\]\]|";
        preg_match_all($pattern, $line, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            $list = current($matches);
            for($i = 0; count($list) > $i; $i += 2) {
                if (!in_array($list[$i+1], $this->linkwords)) {
                    $replace_to = "<a href=\"{$this->base_url}/?mode=edit&amp;pagename={$list[$i+1]}\">{$list[$i+1]}</a>";
                } else {
                    $replace_to = "<a href=\"{$this->base_url}/{$list[$i+1]}\">{$list[$i+1]}</a>";
                }
                $line = str_replace($list[$i], $replace_to, $line);
            }
        }

        return $line;
    }

    function parseH($line)
    {
        preg_match("/^(\*{1,4})(.*)/", $line, $matches);

        $level = $this->h_start_level + strlen($matches[1]) -1;
        $text = trim($this->parseInline($matches[2]));
        
        $key = "k" . substr(md5($text), 0, 7);
        return "<h{$level} id=\"{$key}\">{$text}</h{$level}>";

    }

    function parsePre(&$lines)
    {
        $value = htmlspecialchars($this->takeBlock($lines, "/^\s/"), ENT_QUOTES);
        if (!empty($value)) {
            $line = "<pre><code>{$value}</code></pre>";
        } else {
            $line = "";
        }
        
        return $line;
    }

    function parseQuote(&$lines)
    {
        $value = $this->takeBlock($lines, "/^>/");
        return "<blockquote><p>\n{$value}\n</p></blockquote>";
    }

    /**
     * parseList
     */
    function parseList($type, &$lines, $regexp)
    {
        $buf = array();
        $buf[] = "<{$type}>";

        $value = explode("\n", $this->takeBlock($lines, $regexp));

        while(!is_null($line = array_shift($value))) {

            if (preg_match($regexp, $line, $matches)) {
                array_unshift($value, $line);
                $result = explode("\n", $this->parseList($type, $value, $regexp));
                $buf = array_merge($buf, $result);
                $buf[] = "</li>";
            } else {
                $buf[] = "<li>" . $this->parseInline($line) . "</li>";
            }

        }

        $buf[] = "</{$type}>";

        return implode("\n", $buf);
    }

    //{{{ parseDl
    function parseDl(&$lines, $regexp)
    {
        $buf = array();
        $buf[] = "<dl>";
        
        $value = explode("\n", $this->takeBlock($lines, $regexp));

        foreach ($value as $line) {
            $list = explode("|", $line);

            //@todo B.K (ex. :aaa|)
            if (count($list) != 2) {
                return current($value);
            }

            $buf[] = "<dt>{$list[0]}</dt>";
            $buf[] = "<dd>{$list[1]}</dd>";
        }
        
        $buf[] = "</dl>";
        
        return implode("\n", $buf);
    }
    //}}}

    //{{{ autoLink
    /**
     * autoLink
     *
     * @access private
     * @param string $mes
     * @see http://mt.no22.tk/2006/01/29-22.php
     */
    function autoLink($mes) {
        $strary = preg_split("/(<[\/\!]*?[^<>]*?>)/",
            $mes,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        foreach ($strary as $key => $value) {

            if ($value[0] == "<") {
                continue;
            }

            $strary[$key] = preg_replace(
                "/(https?)(:\/\/[\w\+\$\;\?\-\/\.%,!#~*:@&=]+)/",
                "<a href=\"$0\">$0</a>",
                $value
            );

        }

        return implode("", $strary);
    }
    //}}}

    function parsePlugin($line, $src)
    {
        $match_result = preg_match('/^#(.*?)\((.*?)\)/', $line, $result); 
        if ($match_result === 1) {
          $plugin_name = $result[1];
          $plugin_argument = $result[2];
        }

        if ($match_result === 0) {
          $match_result = preg_match('/^#(\w+)/', $line, $result); 
          $plugin_name = $result[1];
          $plugin_argument = '';
        }

        $plugin_handler = new Text_PukiWiki_PluginHandler();
        $plugin = $plugin_handler->getPlugin($plugin_name, $src);

        if (is_object($plugin)) {
            return $plugin->load($plugin_argument);
        }
    }

    function escapeLink($text)
    {
        global $escapeLink_replace_list;
        $preg_func = create_function('$matches','
            static $number = -1;
            global $escapeLink_replace_list;

            $number++;
            $escapeLink_replace_list[$number] = $matches[0];
            return "[_[[{$number}]]_]";
        ');

        $result = preg_replace_callback(
            '_<(?:pre|h.|a|form).*?>.*?</(?:pre|h.|a|form)>_s',
            $preg_func,
            $text
        );

        return array(
            'replace_list' => $escapeLink_replace_list,
            'text' => $result
        );
    }

    function unescapeLink($text, $linklist)
    {
        if (count($linklist) == 0) {
            return $text;
        }

        foreach($linklist as $key => $value) {
            $text = str_replace("[_[[{$key}]]_]", $value, $text);
        }

        return $text;
    }

}
