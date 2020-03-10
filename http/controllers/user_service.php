<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
require "http/models/User.php";
class user_service extends Controller{
    function __construct(){
        @session_start();
        parent::__construct();
    }
    public function add(){
        if ($_POST){
            $CWFSecurityObj = new CWFSecurity();
            $CWFSecurityObj->validateRequest();
            $UserObj        = new UserModel();
            
            $username       = $_POST['username'];
            $password       = $_POST['password'];
            $parent_id      = $_POST['parent_id'];
            $role           = $_POST['role'];
            
            $data = array(
                "username"      => $username,
                "password"      => $password,
                "parent_id"     => $parent_id,
                "role"          => $role,
                "timestamp"     => time(),
                "mod_timestamp" => time()
            );
            if ($UserObj->add($data)){
                $response = array(
                        "code" => 1,
                        "message" => "Success"
                    );
            }else{
                $response = array(
                        "code" => 0,
                        "message" => "failed"
                    );
            }
            $this->view->response($response);
        }
    }
    public function update(){
        echo "update function";
    }
    public function delete(){
        echo "remove function";
    }
    public function view(){
        $CWFSecurityObj = new CWFSecurity();
        $CWFSecurityObj->validateRequest();
        $UserObj        = new UserModel();
        $response = array(
            "code" => 1,
            "data" => $UserObj->getAll()
        );
    }
}
?>