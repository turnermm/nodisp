<?php
/**
 *   @author Myron Turner <turnermm02@shaw.ca>
 *   @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
*/
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
class action_plugin_nodisp extends DokuWiki_Action_Plugin {
    public function register(Doku_Event_Handler $controller) {   
       $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'handle_wiki_content'); 	    
    }

  function handle_wiki_content(Doku_Event $event, $param) {    
    global $ACT;
    $act = act_clean($ACT);
        if($act == 'source') {      
           $event->data = preg_replace_callback( 
            '|&lt;nodisp (\d+)&gt;.*?&lt;\/nodisp&gt;|ms',
            function($matches) {     
               global $ID;
               $acl = auth_quickaclcheck($ID);
               if($acl < $matches[1]) {
                   return "";
               }          
               return $matches[0];
            },$event->data
         ) ;
          return;
      }
        
     $event->data = preg_replace_callback( 
        '|\<div class\s+=\s+"nodisp_(\d+)">.*?\<\/div>|ms',
        function($matches) {     
           global $ID;
           $acl = auth_quickaclcheck($ID);
           if($acl < $matches[1]) {
               return "";
           }          
           return $matches[0];
        },$event->data
     ) ;
   }

 }
