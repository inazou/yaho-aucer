<?php


class databaseConfig {
    
    /**
     * PDOobject
     * @var object
     */
    private $pdo;
    
    /**
     * databaseData
     * @var array
     */
    private $data =array();
    
            
    
   public function __construct() {
        $xml = topDir . '/xml/database.xml';
        $this->data = json_decode(json_encode(simplexml_load_file($xml)),TRUE);
        $db = "mysql:host={$this->data["host"]};dbname={$this->data["dbname"]};charset=utf8";
       try {
            $this->pdo = new PDO($db, $this->data["user"], $this->data["pass"], array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            error_log("CANNOT CONNECT THE DATABASE:" . $e->getMessage());
            exit('we cannot connect the database');
        }
   }
   
   /**
    * do query without placeholder
    * @access private
    * @return mixed
    */
   private function query($query){
       $this->pdo->query('SET NAMES utf8');
       $stmt = $this->pdo->query($query);
       if (!$stmt) {
            $info = $this->pdo->errorInfo();
            error_log("QUERY ERROR" . print_r($info, TRUE));
            return FALSE;
        }
        $i = 0;
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[$i] = $data;
            $i++;
        }
        return $res;
   }
   
   /**
    * do query with placeholder
    * @access private
    * @param string $query, $type array $param
    * @return mixed
    */
   private function plQuery($query, $param){
        $this->pdo->query('SET NAMES utf8');
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($param);
        if (!$stmt) {
            $info = $this->pdo->errorInfo();
            error_log("QUERY ERROR" . print_r($info, TRUE));
            return FALSE;
        }
        $i = 0;
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[$i] = $data;
            $i++;
        }
        return $res;
   }
   
   /**
    * insert into prefectures 
    * @access public
    * @param $param array
    * @return mixed 
    */
    public function insertPrefectures($param){
        $query = "INSERT INTO prefectures (id, name) VALUES (?, ?)";
        $res = $this->plQuery($query, $param);
        return $res;
    }
    
    /**
    * insert into category 
    * @access public
    * @param $param array
    * @return mixed 
    */
    public function insertCategory($param){
        $query = "INSERT INTO `category` (`id`, `name`, `parentId`) VALUES (?, ?, ?)";
        $res = $this->plQuery($query, $param);
        return $res;
    }
    
    /**
    * select id, name from category where parentId 
    * @access public
    * @param $param array
    * @return mixed 
    */
    public function getCategory($param){
        $query = "SELECT `id`, `name` FROM `category` WHERE `parentId` = ?";
        $res = $this->plQuery($query, $param);
        return $res;
    }

    /**
    * select * from prefectures  
    * @access public
    * @param $param array
    * @return mixed 
    */
    public function getPrefecturesAll(){
        $query = "SELECT * FROM prefectures";
        $res = $this->query($query);
        return $res;
    }
}
