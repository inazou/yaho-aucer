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
     * ヤフーAPIからカテゴリデータを取得し、データベースにインサート
     * @access private
     */
    private function getCategory(){
        $param = array("output" => "xml");
        $xml1 = $this->send($param, self::sendUrl);
        error_log($xml1);
        $res1 = $this->scrapCategoryXml($xml1);
        error_log(print_r($res1, TRUE));
        foreach ($res1 as $val1){
            $this->insert($val1);
            $param["category"] = $val1["category"];
            $xml2 = $this->send($param, self::sendUrl);
            error_log($xml2);
            $res2 = $this->scrapCategoryXml($xml2);
            foreach ($res2 as $val2){
                //$this->insert($val2);
            }
        }
    }
    
    /**
     * カテゴリ情報をデータベースにインサート
     * @param $val array
     * @access private
     */
    private function insert($val){
        $param = array($val["CategoryId"],
                $val["CategoryName"],
                $val["ParentId"]);
        $this->db->insertCategory($param);
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
