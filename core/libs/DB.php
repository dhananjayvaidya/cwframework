<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
class DB extends PDO{
    public $TableName;
    private $ins_flag;
    function __construct($t){
        /**
         * @init model
         * Here we need to check whether the table i.e model exist in the database, 
         * if not exist then need to create it. 
         * 
         *  */
        
        parent::__construct(_DNS_, _USER_, _PASS_);
        $this->TabelName = $t->TableName;
        $this->createTable($t->TableName, $t->Schema);

    }
    public function createTable($tableName,$schema){
        $state = "SHOW TABLES";
        $query = $this->prepare($state);
        $query->execute();
        $tables = $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($tables) == 0){
            return $this->_setupTable($schema,$tableName);
        }else{
            if ($this->_checkTable($tables, $tableName) ){
                return true;
            }else{
                return $this->_setupTable($schema,$tableName);
            }
        }
    }
    public function _checkTable($_tables, $_tableName){
        foreach($_tables as $table){
            if ($table['Tables_in_'._DB_] == $_tableName){
                return true;
            }else{
                return false;
            }
        }
    }
    public function _setupTable($schema,$tableName){
        //create the table here
        $tableDef = "";
        foreach($schema as $key=>$s){
            $tableDef .=  "`".$key."` ".
            ($s['dataType'] == 'string'?'varchar':$s['dataType']).
            (isset($s['dataSize']) ? "(".$s['dataSize'].") ": ""). 
            ($s['not_null'] == true ? "NOT NULL ": "" ). 
            ($s['primary_key'] == true ? 'PRIMARY KEY ':"").
            ($s['unique'] == true ? 'UNIQUE ':"").
            ($s['auto_increment'] == true ? 'AUTO_INCREMENT ':"").
            (isset($s['default']) ? "DEFAULT '".$s['default']."'" : "").",";
        } 
        $tableDef = rtrim($tableDef,",");
        $state = "CREATE TABLE `".$tableName."` (
                    ".$tableDef."
           ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
           //echo $state;
        $query = $this->prepare($state);
        return $query->execute();
    }
    public function getCount(){
        $state = "SELECT COUNT(`id`) AS `c` FROM `".$this->TableName."` WHERE `etms` = 0 ";
        $query = $this->prepare($state);
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        //print_r($data);
        return ($data[0]['c']?$data[0]['c']:0);
    }
    /** 
     * @CRUD Functions
    */
    public function add($data){
        $QueryCols = "";
        $QueryVals = "";
        foreach($data as $key => $val){
            $QueryCols .= "`".$key."`,";
            $QueryVals .= "'".addslashes($val)."',";
        }
        $QueryCols = rtrim($QueryCols,",");
        $QueryVals = rtrim($QueryVals,",");
        $state = "INSERT INTO `".$this->TableName."`( 
                        ".$QueryCols."
                    ) VALUES (
                        ".$QueryVals."
                    )";
        //echo $state;
        $query = $this->prepare($state);
        $query->execute();
        return $this->lastInsertId();
    } 
    public function remove($id){
        $state = "DELETE FROM `".$this->TableName."` WHERE `id` = '".$id."'";
        $query = $this->prepare($state);
        return $query->execute();
    }
    public function update($data,$id){
        $QueryUpdate = "";
        foreach($data as $key => $val){
            $QueryUpdate .= "`".$key."` = '".addslashes($val)."',";
        }
        $QueryUpdate = rtrim($QueryUpdate,",");
        $state = "UPDATE `".$this->TableName."` SET 
                   ".$QueryUpdate."
                WHERE 
                    `id` = '".$id."'
                ";
        $query = $this->prepare($state);
        return $query->execute();
    } 
    public function getById($id){
        $state = "SELECT * FROM `".$this->TableName."` WHERE `id` = '".$id."'";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAll(){
        $state = "SELECT * FROM `".$this->TableName."`";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>