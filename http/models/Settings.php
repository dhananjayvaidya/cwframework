<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SettingsModel extends DB {
    public $TableName = "settings";
    public $Schema = array(
        'id'            => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "primary_key" => true,
            'auto_increment' => true
        ),
        "setting_name"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true,
            "unique" => true
        ),
        "setting_value" => array(
            "dataType"=>"text", 
            
        ),
        "status"     => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        
        "timestamp" => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        "mod_timestamp" => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        "etms"          => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        )
    );
    function __construct() {
    	//@session_start();
        parent::__construct($this);
        $this->add(array(
                "setting_name" => "_PHONE_NUMBER_",
                "setting_value" => "1234567890"
            ));
        $this->add(array(
                "setting_name" => "_TWITTER_LINK_",
                "setting_value" => ""
                ));
        $this->add(array(
                "setting_name" => "_FACEBOOK_LINK_",
                "setting_value" => ""
                ));
        $this->add(array(
                "setting_name" => "_YOUTUBE_LINK_",
                "setting_value" => ""
                ));
        $this->add(array(
                "setting_name" => "_GOOGLE_PLUS_LINK_",
                "setting_value" => ""
                ));
        $this->add(array(
                "setting_name" => "_EMAIL_ADDRESS_",
                "setting_value" => "info@divineair.in"
                ));
        $this->add(array(
                "setting_name" => "_OPEN_HOURS_",
                "setting_value" => "9.30am to 6.30pm"
                ));
    }
    public  function getByName($n){
        $state  = "SELECT * FROM `".$this->TableName."` WHERE `setting_name` = '".$n."' ";
        $query = $this->prepare($state);
        $query->execute();
        $d = $query->fetchAll(PDO::FETCH_ASSOC);
        return $d[0]['setting_value'];
    }
    
}

?>