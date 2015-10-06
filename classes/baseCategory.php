<?php

class baseCategory extends basePage{
    
    public function __construct() {
        parent::__construct();
        //リファラーチェック
        if($_SERVER["REMOTE_ADDR"] == '::1'){
           //postチェック
            
            
            error_log($_POST['val']);
            error_log($_SERVER["REMOTE_ADDR"]);
            echo "<option value=\"1\">内容</option>";
        }else{
            error_log('Warn:REFERRER ERROR');
        }
    }
    
    private function chkPost(){
    }
    
    
    
    
    
}
