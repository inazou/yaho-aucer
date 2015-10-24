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
    * @return mixed
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
    * do query with placeholder
    * @access private
    * @param string $query, $type array $param
    * @return mixed
    */
   private function plQuery($query, $param, $type){
       $stmt = $this->pdo->prepare($query);
       if(count($param) != strlen($type)){
            error_log("QUERY ERROR PARAM:" . print_r($param, TRUE));
            return FALSE;
       }
       for($i = 0; $i < count($param); $i++){
           if(substr($type, $i, 1) == "i"){
               $val = (int)$param[$i];
               $pdo = PDO::PARAM_INT;
            }elseif(substr($type, $i, 1) == "s") {
                $val = (string)$param[$i];
               $pdo = PDO::PARAM_STR;
            } else{
                error_log("QUERY ERROR TYPE:" . $type);
                return FALSE;
            }
           $stmt->bindParam($i + 1, $val, $pdo);
       }
       $stmt->execute();
   }
   
   
    public function insertPrefectures($id , $name){
        $stmt = $this->pdo->prepare("INSERT INTO prefectures (id, name) VALUES (?, ?)");
        $this->pdo->query('SET NAMES utf8');
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $name);
        $stmt->execute();
    }
    
    public function insertCategory($param){
        $this->con();
        $query = "INSERT INTO `category` (`id`, `name`, `parentId`) VALUES (?, ?, ?)";
        $type = "isi";
        $this->plQuery($query, $param, $type);
    }
    
    
    
    public function getPrefecturesAll(){
        $this->con();
        $query = "SELECT * FROM prefectures";
        $res=$this->query($query);
        return $res;
    }
}
