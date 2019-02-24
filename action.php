<?php
/**
 *   @author Myron Turner <turnermm02@shaw.ca>
 *   @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
*/
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
class action_plugin_nodisp extends DokuWiki_Action_Plugin {
   	private   $accumulator = null;
    private $current;
    public function register(Doku_Event_Handler $controller) {   
       $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'handle_wiki_content'); 	    
    }


  function handle_wiki_content(Doku_Event $event, $param) {  
     global $ACT,$ID;
	 msg($ID);
	 $acl = auth_quickaclcheck($ID);
	 msg($acl);
     if($this->getConf('conceal')) {
     $event->data = preg_replace('#\<em\s+class=(\"|\')u(\1)\>\s*BROKEN\-LINK\:(.*?)LINK\-BROKEN\s*</em>#',"$3",$event->data);
     }
   }

 }
