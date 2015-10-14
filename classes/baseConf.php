<?php


class baseConf extends basePage{
    
    
    
    const sendUrl = "http://auctions.yahooapis.jp/AuctionWebService/V2/search";
    
    /**
     *カテゴリーのhtmlを格納
     * @var string
     */
    private $category = "";
    
    /**
     *並べ替えのhtmlを格納
     * @var string
     */
    private $sort = "";
    
    /**
     *出品地域のhtmlを格納
     * @var string
     */
    private $locId = "";
    
    /**
     * メッセージを格納
     * @var string 
     */
    private $msg = "";


    /**
     * 検索結果のhtmlを格納
     * @var string 
     */
    private $result = "";
    
    
    /**
     * 検索結果の商品
     * @var string
     */
    private $resItem = "";
    
    /**
     * メッセージ部分のページを表示
     * @var string
     */
    private $msgPage = "";
    /**
     * GETで取得したデータ
     * @var array
     */
    private $data = array();
    
    /**
     * 検索結果のページ数
     * @var int
     */
    private $maxPage = 0;
    
    /**
     * ページャーhtml
     * @var string
     */
    private $pager = "";
    /**
     * データベース
     * @var Object
     */
    private $db;
    
    public function __construct() {
        parent::__construct();
        include_once ('./classes/databaseConfig.php');
        $this->db = new databaseConfig();
        if(isset($_GET)){
            $this->checkGet(); 
            $this->pager = $this->createPage();
        } else {
            $this->result = "ヤフーオークションからの検索結果を表示します。";
        }
        $this->createCategory();
        $this->createSort();
        $this->createLocId();
        
        
            
        
    }
    
    
    private function checkGet(){
        $cnv = array();
        foreach ($_GET as $key => $value) {
            $cnv[$key] = mb_convert_kana((mb_convert_encoding($value, "UTF-8", "auto" )), "aKV"); 
        }
        unset($key, $value);
        //var_dump($cnv);
        $post = $this->escapeNullByte($cnv);
        $p = print_r($post, TRUE);
        if(!isset($post['query'])){
            $this->result = "ヤフーオークションからの検索結果を表示します。";
            return;
        } elseif ($post['query'] == "") {
            $this->result = "検索する文字列を入力してください。";
            return;
        } else {
            $this->result = '検索ワード:"'. htmlspecialchars($post['query'], ENT_QUOTES, 'UTF-8') . '"';
        }
        $this->data = $post;
        $post['output'] = "xml";
        $res = $this->send($post);
        $this->createDisp($res);
        

    }
    /**
     * curlでpost
     * @access private
     * @param array $param
     * @return mixed
     */
    private function send($param) {
        
        $ch = curl_init(self::sendUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, "Yahoo AppID: ". $this->appid);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        
        $res = curl_exec($ch);
        curl_close($ch);
        if($res == FALSE){
            $this->result = "検索結果の取得に失敗しました。時間をおいて再度検索してください。";
            return FALSE;
        }  else {
            return $res;
        }
        
        
        
        
    }
    /**
     * 詳細検索のカテゴリを作成
     * @access private
     * @param  
     */
    private function createCategory(){
        $cat = array('コンピュータ' => 23336, 
            '家電、AV、カメラ' => 23632, 
            '音楽' => 22152, 
            '本、雑誌' => 21600, 
            '映画、ビデオ' => 21964, 
            'おもちゃ、ゲーム' => 25464,
            'ホビー、カルチャー' => 24242,
            'アンティーク、コレクション' => 20000,
            'スポーツ、レジャー' => 24698,
            '自動車、オートバイ' => 26318,
            'ファッション' => 23000,
            'アクセサリー、時計' => 23140,
            'ビューティー、ヘルスケア' => 42177,
            '食品、飲料' => 23976, 
            '住まい、インテリア' => 24198,
            'ペット、生き物' => 2084055844,
            '事務、店舗用品' => 22896,
            '花、園芸' => 26086,
            'チケット、金券、宿泊予約' => 2084043920,
            'ベビー用品' => 24202,
            'タレントグッズ' => 2084032594,
            'コミック、アニメグッズ' => 20060,
            '不動産' => 2084060731,
            'チャリティー' => 2084217893,
            'その他' => 26084);
        
        foreach ($cat as $key => $value) {
            $this->category .= '<option value ="'. $value . '">'. $key . '</option>';
        }
        
        return TRUE;
    }
    
    /**
     * 詳細検索のソートを作成
     * @access private
     * @param  
     */
    private function createSort(){
        // 画像の有無、アフィリエイトも選択できるが、今はしない
        $so = array('終了時間' => 'end',  
            '入札数' => 'bids', 
            '現在価格' => 'cbids', 
            '即決価格' => 'bidorbuy');
        
        foreach ($so as $key => $value) {
            $this->sort .= '<option value ="'. $value . '">'. $key . '</option>';
        }
        
        return;
    }
    /**
     * 詳細検索の出品地域を作成
     * @access private
     * @param  
     */
    private function createLocId(){
        $res = $this->db->getPrefecturesAll();
        for($i = 0; $i < count($res); $i++){
            $this->locId .= "<option value =\"{$res[$i]["id"]}\">{$res[$i]["name"]}</option>";
        }
        return;
    }
    /**
     * 検索結果をスクレーピングしhtmlを作成
     * @access private
     * @param string
     * @return 
     */
    private function createDisp($xml){
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->formatOutput = TRUE;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'urn:yahoo:jp:auc:search');
        $totalResultsAvailable = $xpath->evaluate('string(//x:ResultSet/@totalResultsAvailable)');
        error_log($totalResultsAvailable);
        $this->msg = "検索結果: ". number_format($totalResultsAvailable). "件";
        if($totalResultsAvailable == 0){
            return;
        }
        $totalResultsReturned = $xpath->evaluate('string(//x:ResultSet/@totalResultsReturned)');
        //echo $totalResultsReturned.'<br>';
        $firstResultPosition = $xpath->evaluate('string(//x:ResultSet/@firstResultPosition)');
        //echo $firstResultPosition.'<br>';
        //ページの最大数を計算
        if(($totalResultsAvailable % 20) === 0){
            $this->maxPage = (int)($totalResultsAvailable / 20);
        } else {
            $this->maxPage = ((int)($totalResultsAvailable / 20) + 1);
        }
        $itemcount = $xpath->query('//x:Result/x:Item')->length;
        //print_r($itemcount);
        for($i = 1;$i <= $itemcount;$i++){
            $resarr[$i]['AuctionID'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:AuctionID)');
            $resarr[$i]['Title'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Title)');
            $resarr[$i]['Id'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Seller/x:Id)');
            $resarr[$i]['AuctionItemUrl'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:AuctionItemUrl)');
            $resarr[$i]['Image'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Image)');
            $resarr[$i]['ImageWidth'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Image/@width)');
            $resarr[$i]['ImageHeight'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Image/@height)');
            $resarr[$i]['CurrentPrice'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:CurrentPrice)');
            $resarr[$i]['Bids'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Bids)');
            $resarr[$i]['EndTime'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:EndTime)');
            $resarr[$i]['BidOrBuy'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:BidOrBuy)');
            $icon[$i]['FeaturedIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:FeaturedIcon)');
            $icon[$i]['NewIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:NewIcon)');
            $icon[$i]['StoreIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:StoreIcon)');
            $icon[$i]['CheckIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:CheckIcon)');
            $icon[$i]['PublicIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:PublicIcon)');
            $icon[$i]['FreeshippingIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:FreeshippingIcon)');
            $icon[$i]['NewItemIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:NewItemIcon)');
            $icon[$i]['WrappingIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:WrappingIcon)');
            $icon[$i]['BuynowIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:BuynowIcon)');
            $icon[$i]['EasyPaymentIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:EasyPaymentIcon)');
            $icon[$i]['GiftIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:GiftIcon)');
            $icon[$i]['PointIcon'] = $xpath->evaluate('string(//x:Result/x:Item['. $i .']/x:Option/x:PointIcon)');
            
            
        }
        //var_dump($reaarr);
        $this->createItemHtml($resarr, $icon);
        return;
    }
    
    /**
     * 検索結果のアイテムの表示用htmlを作成
     * @access private
     * @param array $res
     */
    private function createItemHtml($res, $icon){
        for($i = 1; $i <= count($res); $i++){
            $this->resItem .= "<article><a href=\"". $res[$i]['AuctionItemUrl']. "\"target=/”_blank/”><h1>". $res[$i]['Title']. " ";
            foreach ($icon[$i] as $key => $val){
                if(!empty($icon[$i][$key])){
                    $this->resItem .= "<img src=\"". $val. "\" /> ";
                }
            }
            $this->resItem .= "</h1><figure><div><img src=\"". $res[$i]['Image']. "\" width=\"". $res[$i]['ImageWidth']. "\" height=\"". $res[$i]['ImageHeight']. "\" alt=\"\" /></div></figure><div class=\"leftItem\"><p>出品者: ". $res[$i]['Id']. "</p><p>現在価格: ". number_format(floor($res[$i]['CurrentPrice'])). "円</p>";//<p>入札件数: xx</p><p>残り時間: x日</p>;
            if(!empty($res[$i]['BidOrBuy'])){
                $this->resItem .= "<p>即決価格: ". number_format(floor($res[$i]['BidOrBuy'])). "円</p>";
            }
            $this->resItem .= "</div><div class=\"rightItem\"><p>入札件数: ". $res[$i]['Bids']. "</p><p>残り時間: ". $this->createTime($res[$i]['EndTime']). "</p></div>";
            
            $this->resItem .= "</a></article>";
        }
        return;
    }
    
    
    /**
     * RFC3339をunixtimeに変換して残り時間を計算
     * @access private
     * @param string $time
     * @return string
     */
    private function createTime($time){
        list($year, $month, $day) = explode("-", $time);
        list($day, $h) = explode("T", $day);
        list($h, $m, $s) = explode(":", $h);
        list($s, $t) = explode("+", $s);
        $endtime = mktime($h, $m, $s, $month, $day, $year);
        $end = $endtime - time();
        $d = $end / 3600 / 24;
        $h = $end / 3600;
        $m = $end / 60 - floor($h)*60;
        $h = $end / 3600 - floor($d)*24;
        $date = "";
        if(floor($d) != 0){
            $date .= floor($d). "日 "; 
        }
        $date .= floor($h). "時間". floor($m). "分";
        return $date;
    }
    
    /**
     * ページャーhtml作成
     * @access private
     * @return string ページャーhtml
     */
    private function createPage() {
        if(empty($this->data)){
            return ;
        }
        $url = "./conf.php?";
        foreach($this->data as $key => $val){
            if($key != "page"){
                $url .= $key . "=" . $val . "&";
            }
        }
        $url .= "page=";
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $pager = <<<EOF
                <ul class="pageNav01">
EOF;
        if(empty($this->data["page"])){
            $this->data["page"] = 1;
        }
        // 現在のページの表示を作る
        if($this->maxPage != 0){
            $this->msgPage .= $this->data["page"]. "/";
            $this->msgPage .= $this->maxPage . "ページ";
        }
        if($this->data["page"] == 1){
            //1ページ目の時
            $pager .= <<<EOF
                    <li><a style="display:none">&laquo; 前</a></li>
                    <li><span style="color: black">1</span></li>
EOF;
            for($i = 2; $i <= 5 && $i <= $this->maxPage; $i++){
                $pager .= <<<EOF
                        <li><a href="{$url}{$i}">{$i}</a></li>
EOF;
            }
        } else {
            $page = $this->data["page"] - 1;
            $pager .= <<<EOF
                    <li><a href="{$url}{$page}">&laquo; 前</a></li>
EOF;
            if($this->data["page"] == 2){
                //2ページ目の時
                $pager .= $this->commonCreatePager($url, $page, 0, 5);
            } elseif($this->data["page"] == $this->maxPage) {
                //最終ページの時
                $pager .= $this->commonCreatePager($url, $page, 3, 4);
            } elseif($this->data["page"] == $this->maxPage - 1) {
                //最終ページの１ページ前の時
                $pager .= $this->commonCreatePager($url, $page, 2, 4);
            } else {
                //その他のページの時
                $pager .= $this->commonCreatePager($url, $page, 1, 4);
            }
            
        }
        if($this->data["page"] < $this->maxPage){
            $page = $this->data["page"] + 1;
            $pager .= <<<EOF
                    <li><a href="{$url}{$page}">次 &raquo;</a></li>
EOF;
        }
        $pager .= <<<EOF
                </ul>
EOF;
        return $pager;
    }
    
    /**
     * ページャーhtml作成共通部分
     * @access private
     * @param int $url $page $ini $count
     * @return string ページャーhtml
     */
    private function commonCreatePager($url, $page, $ini, $count){
        $pager ="";
        for($i = $page - $ini; $i < $page + $count && $i <= $this->maxPage; $i++){
            if($i == $this->data["page"]){
                $pager .= <<<EOF
                        <li><span style="color: black">{$i}</span></li>
EOF;
            } else {
                $pager .= <<<EOF
                        <li><a href="{$url}{$i}">{$i}</a></li>
EOF;
            }
        }
        return $pager;
    }

    /**
     * カテゴリのhtmlを取得
     * @access public
     * @return type
     */
    public function getCategory(){
        return $this->category;
    }
    
    /**
     * ソートのhtmlを取得
     * @access public
     * @return type
     */
    public function getSort(){
        return $this->sort;
    }
    
    /**
     * 出品地域のhtmlを取得
     * @access public
     * @return type
     */
    public function getLocId(){
        return $this->locId;
    }
    
    /**
     * 表示用の検索結果を取得
     * @access public
     * @return type
     */
    public function getResult(){
        return $this->result;
        
    }
    /**
     * 表示用の検索結果のアイテムを取得
     * @access public
     * @return type
     */
    public function getResItem(){
        return $this->resItem;
    }
    
    /**
     * 表示用の検索結果の全件数を取得
     * @access public
     * @return type
     */
    public function getMsg(){
        return $this->msg;
    }
    
    /**
     * 表示用の検索結果のページを取得
     * @access public
     * @return type
     */
    public function getMsgPage(){
        return $this->msgPage;
    }
    
    /**
     * 表示用の検索結果のページを取得
     * @access public
     * @return type
     */
    public function getPager(){
        return $this->pager;
    }
    
}

