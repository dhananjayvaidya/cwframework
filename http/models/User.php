<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class UserModel extends DB {
    public $TableName = "users";
    public $Schema = array(
        'id'            => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "primary_key" => true,
            'auto_increment' => true
        ),
        "username"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
        ),
        "password"      => array(
            "dataType"=>"string", 
            "dataSize"=>255, 
            "not_null"=>true
        ),
        "parent_id"     => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        "role"          => array(
            "dataType"=>"string", 
            "dataSize"=>200, 
            "not_null"=>true
        ),
        "timestamp"     => array(
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
    public function checkUsername($username){
        $state = "SELECT * FROM `users` WHERE `username` = '".$username."'";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function checkUser($username,$password){
        $state = "SELECT * FROM `users` WHERE `username` = '".$username."' AND `password` = '".$password."'";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function add($data){
        $state = "INSERT INTO `users`( 
                        `username`, 
                        `password`, 
                        `parent_id`, 
                        `role`, 
                        `timestamp`, 
                        `mod_timestamp`
                    ) VALUES (
                        '".$data['username']."', 
                        '".$data['password']."', 
                        '".$data['parent_id']."', 
                        '".$data['role']."', 
                        '".time()."', 
                        '".time()."'
                    )";
        $query = $this->prepare($state);
        return $query->execute();
    } 
    public function remove($id){
        $state = "DELETE FROM `users` WHERE `id` = '".$id."'";
        $query = $this->prepare($state);
        return $query->execute();
    }
    public function update($data){
        $state = "UPDATE `users` SET 
                    `username`      = '".$data['username']."',
                    `password`      = '".$data['password']."',
                    `parent_id`     = '".$data['parent_id']."',
                    `role`          = '".$data['role']."',
                    `timestamp`     = '".time()."',
                    `mod_timestamp` = '".time()."',
                  WHERE 
                    `id` = '".$data['id']."'
                  ";
        $query = $this->prepare($state);
        return $query->execute();
    } 
    public function getById($id){
        $state = "SELECT * FROM `users` WHERE `id` = '".$id."'";
        $query = $this->prepare($state);
        return $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAll(){
        $state = "SELECT * FROM `users`";
        $query = $this->prepare($state);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>