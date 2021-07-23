<?php
/**
 * Alternate span syntax component for the wrap plugin
 *
 * Defines  <wrap> ... </wrap> syntax
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Anika Henke <anika@selfthinker.org>
 */

require_once(dirname(__FILE__).'/basic.php');


class syntax_plugin_nodisp_stack extends syntax_plugin_nodisp_basic {
  //protected  $p_type = 'stack';
   function getPType(){ return  'stack'; }
    function connectTo($mode) { $this->Lexer->addEntryPattern('<nodisp.*?>(?=.*?</nodisp>)',$mode,'plugin_nodisp_stack'); }
    function postConnect() { $this->Lexer->addExitPattern('</nodisp>','plugin_nodisp_stack'); }

}

