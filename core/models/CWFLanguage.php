<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
class CWFLanguageModel extends DB{
    public $TableName = "system_language";
    public $Schema    = array(
        "id" => array(
            "dataType" => "int",
            "dataSize" => 11,
            "auto_increment" => true,
            "primary_key" => true
        ),
        "lang_def" => array(
            "dataType" => "string",
            "dataSize" => "255"
        ),
        "en" => array(
            "dataType" => "text"
        ),
        "hi" => array(
            "dataType" => "text"    
        ),
        "timestamp" => array(
            "dataType" => "int",
            "dataSize" => "11",
            "default"  => 0
        ),
        "mod_timestamp" => array(
            "dataType" => "int",
            "dataSize" => "11",
            "default"  => 0
        ),
        "etms" => array(
            "dataType" => "int",
            "dataSize" => "11",
            "default"  => 0
        )
    );
    function __construct(){
        parent::__construct($this);
    }
    function checkDefination($str){
        $state = "SELECT COUNT(`id`) AS `c` FROM `".$this->TableName."` WHERE `lang_def` = '".$str."'";
        $query = $this->prepare($state);
        $query->execute();
        $d = $query->fetchAll(PDO::FETCH_ASSOC);
        return ($d[0]['c'] >= 1 ? true: false);
    }
    function getByLangDef($str){
        $state = "SELECT * FROM `".$this->TableName."` WHERE `lang_def` = '".$str."'";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>