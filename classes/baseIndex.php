<?php

class baceIndex extends basePage{
    
    /**
     *カテゴリーのhtmlを格納
     * @var string
     */
    private $category = "";
            
    function __construct() {
        parent::__construct();
        $this->createCategory();
    }
    
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
        
        return;
    }
    
    public function getCategory(){
        return $this->category;
    }
}

