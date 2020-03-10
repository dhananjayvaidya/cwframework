<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MediaModel extends DB {
    public $TableName = "media";
    public $Schema = array(
        'id'            => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "primary_key" => true,
            'auto_increment' => true
        ),
        "file_name"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
        ),
        "file_size"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
        ),
        "file_type"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
        ),
        "file_location"  => array(
            "dataType"=>"text"
            
        ),
        "media_type"     => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
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
    }
    
}

?>