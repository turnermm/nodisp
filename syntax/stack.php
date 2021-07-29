<?php

require_once(dirname(__FILE__).'/basic.php');


class syntax_plugin_nodisp_stack extends syntax_plugin_nodisp_basic {
   function getPType(){ return  'stack'; }
    function connectTo($mode) { $this->Lexer->addEntryPattern('<nodisp.*?>(?=.*?</nodisp>)',$mode,'plugin_nodisp_stack'); }
    function postConnect() { $this->Lexer->addExitPattern('</nodisp>','plugin_nodisp_stack'); }

}

