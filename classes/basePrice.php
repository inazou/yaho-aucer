<?php

class basePrice extends basePage{
    
    const sendUrl1 = "http://auctions.yahooapis.jp/AuctionWebService/V2/search";
    
    public function __construct() {
        parent::__construct();
        //リファラーチェック
        $referer = $_SERVER["HTTP_REFERER"];
        $url = parse_url($referer);
        $host = $url["host"];
        if($host == "yahoaucer.jpn.ph"){
            error_log(print_r($_POST, TRUE));
        }else{
            error_log('REFERRER ERROR:'. $_SERVER["REMOTE_ADDR"]);
            $this->html = "<option value=\"\">データ取得に失敗しました</option>";
        }
    }
}

