<?php

class baseCategory extends basePage{
    
    const sendUrl = "http://auctions.yahooapis.jp/AuctionWebService/V2/categoryTree";
    
    /**
     * データベース
     * @var databaseConfig
     */
    private $db;
    
    /**
     * サブカテゴリのoptionタグ
     * @var string
     */
    private $html = "";
    
    public function __construct() {
        parent::__construct();
        //リファラーチェック
        $referer = $_SERVER["HTTP_REFERER"];
        $url = parse_url($referer);
        $host = $url["host"];
        if($host == "yahoaucer.jpn.ph" || "localhost"){
            $data = $this->chkPost($_POST);
            if(empty($data["val"])){
                $this->html = "<option value=\"\">カテゴリを選択してください</option>";
            } else if($data !== FALSE){
                $this->db = new databaseConfig();
                //$this->getCategory();
                $this->html = $this->createHtml($data);
            }else{
                error_log('POST PARAM ERROR:'. print_r($_POST, TRUE));
                $this->html = "<option value=\"\">データ取得に失敗しました</option>";
            }
        }else{
            error_log('REFERRER ERROR:'. $_SERVER["REMOTE_ADDR"]);
            $this->html = "<option value=\"\">データ取得に失敗しました</option>";
        }
    }
    
    /**
     * POSTデータをチェック
     * @param array $post
     * @access private
     * @return mixed
     */
    private function chkPost($post){
        $cnv = array();
        foreach ($post as $key => $value) {
            $cnv[$key] = mb_convert_kana((mb_convert_encoding($value, "UTF-8", "auto" )), "aKV"); 
        }
        unset($key, $value);
        $data = $this->escapeNullByte($cnv);
        if(!empty($data["val"]) && !preg_match("/^[0-9]+$/", $data["val"])){
            return FALSE;
        } 
        if(!empty($data["sVal"]) && !preg_match("/^[0-9]+$/", $data["sVal"])){
            return FALSE;
        }
        return $data;
    }
    
    /**
     * ヤフーAPIからカテゴリデータを取得し、データベースにインサート
     * @access private
     */
    private function getCategory(){
        $param = array("output" => "xml");
        $xml1 = $this->send($param, self::sendUrl);
        $res1 = $this->scrapCategoryXml($xml1);
        foreach ($res1 as $val1){
            $this->insert($val1);
            $param["category"] = $val1["CategoryId"];
            $xml2 = $this->send($param, self::sendUrl);
            $res2 = $this->scrapCategoryXml($xml2);
            foreach ($res2 as $val2){
                $this->insert($val2);
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
    
    /**
     * 親のidから子のカテゴリのoptionタグを生成
     * @access private
     * @param array
     * @return string
     */
    private function createHtml($data){
        $res = $this->db->getCategory(array($data["val"]));
        if($res === FALSE || count($res) == 0){
            return "<option value=\"\">データ取得に失敗しました</option>";
        }
        $html = <<<EOF
                    <option value=\"\">選択なし</option>
EOF;
        foreach ($res as $val){
            $html .= <<<EOF
                    <option value="{$val["id"]}"
EOF;
            if(isset($data["sVal"]) && $data["sVal"] == $val["id"]){
                $html .=<<<EOF
                        selected
EOF;
            }
            $html .= <<<EOF
                    >{$val["name"]}</option>
EOF;
        }
        return $html;
    }
    
    /**
     * サブカテゴリのセレクトボックスの中身を取得
     * @access public
     * @return string
     */
    public function getHtml(){
        return $this->html;
    }
}
