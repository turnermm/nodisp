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
      
      if(file_exists(DOKU_PLUGIN . 'nodisp/syntax.php')) {
               msg("Please remove syntax.php from lib/plugins/nodisp or remove the plugin and reinstall");
      }
     
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
         
         $event->data = preg_replace_callback( 
            '|\{nodisp (\d+)\}.*?\{\/nodisp\}|ms',
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
         '|<div class = "nodisp_(\d+)"><!-- nodisp -->(.*?)<!-- nodisp -->.*?<\/div>|ms',
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
