<?php

class basePage{
    
    
    /*
     * 親のディレクトリを入れる
     * @type String
     */
    public $topDir = NULL;
            
    function __construct() {
        $this->setTopDir();
    }
    
    function setTopDir(){
        
        echo dirname($_SERVER["SCRIPT_NAME"]);
    }
        
    
}

