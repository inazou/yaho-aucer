<?php


class baseConf extends basePage{
    
    
    const appid = "dj0zaiZpPWRzTGF1cmQzamE4TSZzPWNvbnN1bWVyc2VjcmV0Jng9MzI-";
    
    const sendUrl = "http://auctions.yahooapis.jp/AuctionWebService/V2/search";

        public function __construct() {
        parent::__construct();
        
        if(isset($_GET)){
            
            $this->checkGet(); 
            
        }
            
        
    }
    
    
    private function checkGet(){
        $cnv = array();
        foreach ($_GET as $key => $value) {
            
            $cnv[$key] = mb_convert_kana((mb_convert_encoding($value, "UTF-8", "auto" )), "aKV"); 
            
        }
        unset($key, $value);
        var_dump($cnv);
        $post = $this->escapeNullByte($cnv);
        $p = print_r($post, TRUE);
        
        $post['output'] = "xml";
        $this->send($post);
        
        
        
        
    }
    /**
     * curlでpost
     * @access private
     * @param array $param
     * @return boolean 
     */
    private function send($param) {
        
        $ch = curl_init(self::sendUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, "Yahoo AppID: ". self::appid);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        
        $res = curl_exec($ch);
        curl_close($ch);
        var_dump($res);
        
        
        
    }
    
    
}

