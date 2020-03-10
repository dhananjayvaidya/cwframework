<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
require "http/models/User.php";

class admin extends Controller{
    function __construct(){
        
        parent::__construct();
    }
    function first(){
        if ($_POST){
            session_start();
            $UserObj = new UserModel();
            $response = $UserObj->checkUser($_POST['username'],sha1($_POST['password']));
            if (count($response) > 0){
                if ($response[0]['role'] == 'ADMIN'){
                    //set sessions
                    $_SESSION['admin_uid'] = $response[0]['id'];
                    $_SESSION['admin_username'] = $response[0]['username'];
                    $this->view->message = "Success <script>window.reload();</script>";
                }else{
                    $this->view->message = "UnAuthorized Access";
                }
            }else{
                $this->view->message = "Please check the username and password";
            }
        }
        
        if (@$_SESSION['admin_uid']){
        	//get dashboard counts
            $UserObj        = new UserModel();
            
        	$this->view->userCount     = $UserObj->getCount();
        	
			$this->view->title = "Dashboard | "._COMPANY_NAME_;
			$this->view->render("admin/index/dashboard");
			
		}else{
			$this->view->title="Welcome to "._COMPANY_NAME_;
			$this->view->render("admin/index/index");
			
		}
    }
    
    function logout(){
        session_destroy();
        echo "<script>window.location='"._ADMIN_PATH_."';</script>";
    }
}
?>