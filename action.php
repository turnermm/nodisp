<?php
/**
 *   @author Myron Turner <turnermm02@shaw.ca>
 *   @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class action_plugin_nodisp extends DokuWiki_Action_Plugin {
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'handle_wiki_content');
    }

    function __construct() {
        if (file_exists(DOKU_PLUGIN . 'nodisp/syntax.php')) {
            msg("Please remove syntax.php from lib/plugins/nodisp or remove the plugin and reinstall");
        }
    }

    function evaluate_acl($level, $content) {
        global $ID;
        $acl = auth_quickaclcheck($ID);
        /*
         * In case the permission level is above page level (2) and the user has
         * permissions on the namespace, we need to check against the
         * permissions of the namespace.
         */
        if ($level > 2 && auth_quickaclcheck(getNS($ID)) > 2) {
            $acl = auth_quickaclcheck(getNS($ID));
        }
        if ($acl < $level) {
            return "";
        }
        return $content;
    }

    function handle_wiki_content(Doku_Event $event, $param) {
        global $ACT;
        $act = act_clean($ACT);

        if ($act == 'source') {
            $event->data = preg_replace_callback(
                '|&lt;nodisp (\d+)&gt;.*?&lt;\/nodisp&gt;|ms',
                function($matches) {
                    return $this->evaluate_acl($matches[1], $matches[0]);
                }, $event->data
            );

            $event->data = preg_replace_callback(
                '|\{nodisp (\d+)\}.*?\{\/nodisp\}|ms',
                function($matches) {
                    return $this->evaluate_acl($matches[1], $matches[0]);
                }, $event->data
            );

            $event->data = preg_replace_callback(
                '|&lt;nodisp\s+(\w+)&gt;.*?&lt;\/nodisp&gt;|ms',
                function($matches) {
                    return $this->evaluate_acl($this->groupLevel($matches[1]),
                                               $matches[0]);
                }, $event->data
            );

            $event->data = preg_replace_callback(
                '|\{nodisp\s+(\w+)\}.*?\{\/nodisp\}|ms',
                function($matches) {
                    return $this->evaluate_acl($this->groupLevel($matches[1]),
                                               $matches[0]);
                }, $event->data
            );

            return;
        }

        $event->data = preg_replace_callback(
            '|<div class = "nodisp_([\w\d]+)"><!-- nodisp -->(.*?)<!-- nodisp -->.*?<\/div>|ms',
            function($matches) {
                return $this->evaluate_acl($this->groupLevel($matches[1]),
                                           $matches[0]);
            }, $event->data
        );
    }

    function groupLevel($match) {
        global $INFO;

        if (is_numeric($match)) {
            return $match;
        }
        $user_groups = $INFO['userinfo']['grps'];
        if (empty($user_groups)) return "256";
        if (is_array($user_groups)) {
            if (in_array($match,$user_groups)) {
                return "1";
            }
        }
        return "256";
    }
}