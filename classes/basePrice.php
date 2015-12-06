<?php

class basePrice extends basePage{
    
    const sendUrl1 = "http://auctions.yahooapis.jp/AuctionWebService/V2/search";
    
    const sendUrl2 = "http://auctions.yahooapis.jp/AuctionWebService/V2/auctionItem";


    /**
     * 表示用html
     * @var string
     */
    private $html = '<aside class="box1 mb1em"><p>検索結果の入札済み商品出品価格を表示します。</p></aside>';
    
    /**
     * 次のページ検索フラグ
     * @var boolean
     */
    private $search = TRUE;
    
    /**
     * 最大ページ数
     * @var int
     */
    private $maxPage = 0;


    public function __construct() {
        parent::__construct();
        //リファラーチェック
        $referer = $_SERVER["HTTP_REFERER"];
        $url = parse_url($referer);
        $host = $url["host"];
        if($host == "yahoaucer.jpn.ph"){
            $post = filter_input_array(INPUT_POST);
            if(!empty($post)){
                do{
                    $data = $this->chkPost($post);
                    if($data === FALSE){
                        break;
                    }
                    $priceData = $this->bidedItemPrice($data);
                    if($priceData === FALSE){
                        break;
                    }
                    $this->html = $this->createDisp($priceData);
                }while (0);
            }  else {
                error_log('POST DATA IS NOTHING REFERER>>'. $_SERVER["REMOTE_ADDR"]);
            }
        }else{
            error_log('REFERER ERROR:'. $_SERVER["REMOTE_ADDR"]);
        }
    }
    
    
    /**
     * 開始価格の最小と最大と平均を算出し、表示を作成
     * 
     * @access private
     * @param array $priceData
     * @return string
     */
    private function createDisp($priceData){
        $max = number_format(max($priceData));
        $min = number_format(min($priceData));
        $ave = number_format(floor(array_sum($priceData)/count($priceData)));
        $html = <<<EOF
                <aside class="box1 mb1em">
                    <p>検索結果の入札済み商品出品価格<br>
                        最高価格: {$max}円<br>
                        平均価格: {$ave}円<br>
                        最低価格: {$min}円<br>
                        ※出品価格が1円の場合は除いています。
                    </p>
                </aside>
EOF;
        return $html;
    }
        

    /**
     * 入札されているものの開始価格をすべて取得
     * 
     * @access private
     * @param array $data
     * @return mixed
     */
    private function bidedItemPrice($data){
        $i = 0;
        while ($this->search){
            $res = $this->send($data, self::sendUrl1);
            if($res === FALSE){
                return FALSE;
            }
            $arr = $this->scrapSearch($res);
            if($arr === FALSE){
                return FALSE;
            }
            foreach($arr as $val){
                $data2["output"] = "xml";
                $data2["auctionID"] = $val["AuctionID"];
                $res2 = $this->send($data2, self::sendUrl2);
                if($res2 === FALSE){
                    return FALSE;
                }
                // 1円開始を除外する
                $price = $this->scrapAuctionItem($res2);
                if($price != 1){
                    $priceData[$i] = $price;
                    $i++;
                }
            }
            if($data["page"] < $this->maxPage){
                $data["page"]++;
            } else {
                break;
            }
        }
        return $priceData;
    }
    
    /**
     * 商品詳細から開始時の価格をスクレイピング
     * 
     * @access private
     * @param string $xml
     * @return int
     */
    private function scrapAuctionItem($xml) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->formatOutput = TRUE;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'urn:yahoo:jp:auc:auctionItem');
        $price = $xpath->evaluate('string(//x:Result/x:Initprice)');
        return floor($price);
    }
    
    /**
     * 検索結果をスクレーピングし配列にいれる
     * @access private
     * @param string
     * @return mixed
     */
    private function scrapSearch($xml){
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->formatOutput = TRUE;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'urn:yahoo:jp:auc:search');
        if($this->maxPage == 0){
            $totalResultsAvailable = $xpath->evaluate('string(//x:ResultSet/@totalResultsAvailable)');
            if($totalResultsAvailable == 0){
                return FALSE;
            }
            //ページの最大数を計算
            if(($totalResultsAvailable % 20) === 0){
                $this->maxPage = (int)($totalResultsAvailable / 20);
            } else {
                $this->maxPage = ((int)($totalResultsAvailable / 20) + 1);
            }
        }
        $itemcount = $xpath->query('//x:Result/x:Item')->length;
        //print_r($itemcount);
        for($i = 1;$i <= $itemcount;$i++){
            $resarr[$i]['AuctionID'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:AuctionID)');
            $resarr[$i]['Bids'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Bids)');
            if($resarr[$i]['Bids'] == 0){
                unset($resarr[$i]);
                $this->search = FALSE;
                break;
            }
        }
        return $resarr;
    }
       
    /**
     * check post data
     * 
     * @access private
     * @param array $post
     * @return mixed 
     */
    private function chkPost($post){
        $cnv = array();
        foreach ($post as $key => $value) {
            $cnv[$key] = mb_convert_kana((mb_convert_encoding($value, "UTF-8", "auto" )), "aKV"); 
        }
        unset($key, $value);
        $data = $this->escapeNullByte($cnv);
        //check query
        if(empty($data["query"])){
            return FALSE;
        }
        //check category
        if(!empty($data["mCategory"]) && !preg_match("/^[0-9]+$/", $data["mCategory"])){
            return FALSE;
        }
        $data["category"] = $data["mCategory"];
        if(!empty($data["sCategory"]) && preg_match("/^[0-9]+$/", $data["sCategory"])){
            $data["category"] = $data["sCategory"];
        }
        //check store
        if(!preg_match("/^[0-2]$/", $data["store"])){
            return FALSE;
        }
        //check loc_cd
        if(!empty($data["loc_cd"]) && !preg_match("/^[0-9]+$/", $data["loc_cd"])){
            return FALSE;
        }
        //check buynow
        if(!empty($data["buynow"]) && !preg_match("/^[1]$/", $data["buynow"])){
            return FALSE;
        }
        //check item_status
        if(!preg_match("/^[0-2]$/", $data["item_status"])){
            return FALSE;
        }
        //check adf
        if(!preg_match("/^[0-1]$/", $data["adf"])){
            return FALSE;
        }
        $data['output'] = "xml";
        $data["sort"] = "bids";
        $data["order"] = "a";
        $data["page"] = 1;
        return $data;
    }
    
    /**
     * 表示用htmlを取得
     * @access public
     * @return string
     */
    public function getHtml(){
        return $this->html;
    }
}

