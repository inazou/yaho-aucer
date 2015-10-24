<?php

class baseCategory extends basePage{
    
    const sendUrl = "http://auctions.yahooapis.jp/AuctionWebService/V2/categoryTree";
    
    /**
     * データベース
     * @var databaseConfig
     */
    private $db;
    
    public function __construct() {
        parent::__construct();
        //リファラーチェック
        $referer = $_SERVER["HTTP_REFERER"];
        $url = parse_url($referer);
        $host = $url["host"];
        if($host == "yahoaucer.jpn.ph"){
            $this->db = new databaseConfig();
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
    
    /**
     * ヤフーAPIからカテゴリデータを取得し、データベースに格納
     * @access private
     */
    private function getCategory(){
        $param = array("output" => "xml");
        $xml = $this->send($param, self::sendUrl);
        error_log($xml);
        $res = $this->scrapCategoryXml($xml);
        error_log(print_r($res, TRUE));
        foreach ($res as $val){
            $param = array($val["CategoryId"],
                $val["CategoryName"],
                $val["ParentId"]);
            $this->db->insertCategory($param);
        }
        
        
    }
    
    /**
     * xmlをスクレーピングし配列化
     * @access private
     * @param string
     * @return array
     */
    private function scrapCategoryXml($xml){
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->formatOutput = TRUE;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'urn:yahoo:jp:auc:categoryTree');
        $count = $xpath->query('//x:Result/x:ChildCategory')->length;
        for($i = 1;$i <= $count;$i++){
            $resarr[$i]['CategoryId'] = $xpath->evaluate('string(//x:Result/x:ChildCategory['. $i .']/x:CategoryId)');
            $resarr[$i]['CategoryName'] = $xpath->evaluate('string(//x:Result/x:ChildCategory['. $i .']/x:CategoryName)');
            $resarr[$i]['ParentId'] = $xpath->evaluate('string(//x:Result/x:CategoryId)');
        }
        return $resarr;
    }
}
