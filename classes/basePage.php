<?php

class basePage{
    
    
    // テンプレートのディレクトリを保存
    private $tempDir ="";
    

            
    public function __construct() {
        $this->init();
    }
    
    /**
     * initialize
     */
    private function init(){
        $this->setTempDir($this->userAgentCheck());
    } 

    



    /**
     * ユーザーエージェントの判別
     * ガラケー return 2
     * スマホ return 1
     * PC return 0
     * 
     * @return int
     */
    private function userAgentCheck(){
        $ua = $_SERVER['HTTP_USER_AGENT'];
        
        
        
        // iPhoneのチェック
        if(strstr('iPhone', $ua)){
            return 1;
        }
        
        // iPadのチェック
        if (strstr('iPad', $ua)){
            return 1;
        }
        
        // Androidのチェック
        if(strstr('Android', $ua)){
            return 1;
        }
        
        // WindowsPhoneのチェック
        if(strstr('Windows Phone', $ua)){
            return 1;
        }
        
        return 0;
                
                
    }
    
    private function setTempDir($ct){
        
        if($ct == 1){
            $this->tempDir = topDir.'/template/sphone';
        }
        
        if($ct == 0){
            $this->tempDir = topDir.'/template/pc';
        }
        
        return;
    
        
    
    }
    
    
    public function getTempDir(){
        return $this->tempDir;
    }


}
