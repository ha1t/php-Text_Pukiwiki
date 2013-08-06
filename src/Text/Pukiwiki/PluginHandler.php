<?php
/**
 * Text_PukiWiki_PluginHandler
 *
 * @author TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 */
class Text_PukiWiki_PluginHandler
{
    var $plugin_dir;
    var $class_prefix;

    function Text_PukiWiki_PluginHandler()
    {
        $this->plugin_dir = dirname(__FILE__) . "/Plugin";
        $this->class_prefix = "Text_PukiWiki_Plugin_";
    }

    function getPlugin($r_plugin_name, $r_src)
    {
        $plugin_class = ucfirst(basename($r_plugin_name));
        $plugin_path = "{$this->plugin_dir}/{$plugin_class}/{$plugin_class}.php";
        
        if (!file_exists($plugin_path)) {
            return "<strong class=\"error\">Plugin [{$r_plugin_name}] is not found</strong>";
        }

        require_once $plugin_path;
        $plugin_class = $this->class_prefix . $plugin_class;
        $plugin = new $plugin_class($r_src);

        return $plugin;
    }
}
?>
