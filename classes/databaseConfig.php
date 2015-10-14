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
   }
   
   /**
    * connect database
    * @access private
    */
   private function con(){
       $db = "mysql:host={$this->data["host"]};dbname={$this->data["dbname"]};charset=utf8";
       try {
            $this->pdo = new PDO($db, $this->data["user"], $this->data["pass"]);
        } catch (PDOException $e) {
            error_log("CANNOT CONNECT THE DATABASE:" . $e->getMessage());
            exit('we cannot connect the database');
        }
        $this->pdo->query('SET NAMES utf8');
   }
   
   /**
    * do query without placeholder
    * @access private
    * @return bool
    */
   private function query($query){
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
    * disconnect database
    * @access private
    */
   private function discon(){
       $this->pdo = null;
   }
   
    public function insertPrefectures($id , $name){
        $stmt = $this->pdo->prepare("INSERT INTO prefectures (id, name) VALUES (?, ?)");
        $this->pdo->query('SET NAMES utf8');
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $name);
        $stmt->execute();
    }
    public function getPrefecturesAll(){
        $this->con();
        $query = "SELECT * FROM prefectures";
        $res=$this->query($query);
        $this->discon();
        return $res;
    }
}
