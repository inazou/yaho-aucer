<?php

class baseCategory extends basePage{
    
    const sendUrl = "http://auctions.yahooapis.jp/AuctionWebService/V2/categoryTree";
    
    /**
     * データベース
     * @var Object
     */
    private $db;
    
    public function __construct() {
        parent::__construct();
        //リファラーチェック
        if($_SERVER["REMOTE_ADDR"] == '::1'){
            $this->getCategory();
           //postチェック
            error_log($_POST['val']);
            echo "<option value=\"1\">内容</option>";
        }else{
            error_log('REFERRER ERROR:'. $_SERVER["REMOTE_ADDR"]);
        }
    }
    
    private function chkPost(){
    }
    
    
    private function getCategory(){
        $param = array("output" => "xml");
        $res = $this->send($param, self::sendUrl);
        error_log($res);
    }
    
    
}
