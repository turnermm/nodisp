<?php
         
    // must be run within DokuWiki
    if(!defined('DOKU_INC')) die();
     
    if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
    require_once(DOKU_PLUGIN.'syntax.php');
     
    /**
     * All DokuWiki plugins to extend the parser/rendering mechanism
     * need to inherit from this class
     */
    class syntax_plugin_nodisp_basic extends DokuWiki_Syntax_Plugin {
    
        function getType(){ return 'container'; }
        function getAllowedTypes() { return array('formatting', 'substition', 'disabled', 'protected', 'container', 'paragraphs' ); }   
        function getSort(){ return 168; }

        /**
         * Handle the match
         */
        function handle($match, $state, $pos, Doku_Handler $handler){
/*
 none   0
 read   1
 edit   2
 create 4
 upload 8
 delete 16
*/
            switch ($state) {
              case DOKU_LEXER_ENTER : 
                if(preg_match("/nodisp\s+(\w+|\d+)(>|\})/",$match,$m) ){
                    $level = "nodisp_" . $this->getLevel($m[1]);                     
                       return array($state,"<div class = \"$level\"><!-- nodisp -->\n");
                      }                          
                   return array($state, "<div style='display:none'><!-- nodisp -->\n");     
                  
              case DOKU_LEXER_UNMATCHED :  return array($state, $match);
              case DOKU_LEXER_EXIT :   return array($state, '');
            }
       
            return array();
        }
     
        /**
         * Create output
         */
        function render($mode, Doku_Renderer $renderer, $data) {
            global $INFO;
            if($mode == 'xhtml'){
                $renderer->nocache(); // disable caching
                list($state, $match) = $data;
                switch ($state) {
                  case DOKU_LEXER_ENTER :  
                       if($INFO['isadmin'] || $INFO['ismanager'] ) break;   				
			   	       $renderer->doc .= $match;                  
                    break;
                  case DOKU_LEXER_UNMATCHED :  $renderer->doc .= $renderer->_xmlEntities($match); break;
                  case DOKU_LEXER_EXIT : 
                       if($INFO['isadmin'] || $INFO['ismanager'] ) break;   
                    $renderer->doc .= "<!-- nodisp --></div>";
                    break;
                }
                return true;
            }
            return false;
        }
     

        function getLevel($match) {
            global $INFO;
            if(is_numeric($match)) {
                return $match;
            }               

            $user_groups = $INFO['userinfo']['grps'];
            if($user_groups && is_array($user_groups)) {
                if(in_array($match,$user_groups)) {                   
                    return 16;
                }
            }
            return $match; 
        }
}
