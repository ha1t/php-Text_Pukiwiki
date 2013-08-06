<?php
/**
 * vim: sw=4:
 * Rakuten.php
 *
 * @package Text_PukiWiki
 * @author halt feits <halt.feits@gmail.com>
 */

require_once 'Text/PukiWiki/Plugin.php';

/**
 * Rakuten - PukiWiki plugin
 *
 * @package Text_PukiWiki
 * @author halt feits <halt.feits@gmail.com>
 */
class Text_PukiWiki_Plugin_Rakuten extends Text_PukiWiki_Plugin
{
    var $version = '1.0';
    var $use_cache = true;
    var $cache_list = false;
    var $output_encoding = 'UTF-8';

    function load($arg)
    {
        $params = $this->parseArg($arg);

        if ($params[0] == "search") {

            if ($this->use_cache === true) {
                if ($data = apc_fetch("plugin_rakuten:" . $params[1])) {
                    return $data;
                }
            }

            return $this->search($params[1]);
        }

        return $arg;
    }

    function parseArg($arg)
    {
        $params = explode(',', $arg);

        if (!is_array($params) || (count($params) < 2)) {
            //invalid arguments
            return false;
        }

        foreach ($params as $key => $value) 
        {
            $params[$key] = trim($value, " \"'");
        }

        return $params;
    }

    function search($keyword)
    {
        require_once 'Services/Rakuten.php';

        $dev_id = '434637cd52618592fbe80aa3c625a5b6';
        $afi_id = '06b2c372.4935dd5e.06b2c373.d61e8826';

        // 楽天商品検索
        $api = Services_Rakuten::factory('ItemSearch', $dev_id, $afi_id);

        $api->execute(
            array(
                'keyword' => $keyword,
                'availability' => '1',
                'sort' => '+affiliateRate',
                'hits' => '2'
            )
        );

        $data = $api->getResultData();
        if (is_null($data)) {
            $items = array();
        } else if ($data['count'] == 1) {
            $items = $data['Items'];
        } else {
            $items = $data['Items']['Item'];
        }
        $html = "";

        foreach ($items as $item)
        {
            if ($item['imageFlag'] == 1) {
                $html .= "<p>";
                $html .= "<a href=\"{$item['affiliateUrl']}\">";
                $html .= "<img src=\"{$item['mediumImageUrl']}\"><br />";
                $html .= "{$item['itemName']}</a>";
                $html .= "</p>";
            }
        }
        
        if ($this->output_encoding != 'UTF-8') {
            $html = mb_convert_encoding($html, $this->output_encoding, 'UTF-8');
        }

        if ($this->use_cache === true) {
            apc_store("plugin_rakuten:" . $keyword, $html, 3600 * 48);
        }

        return $html;
    }
}
