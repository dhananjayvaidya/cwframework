<?php
//@require "models/account.php";
class Controller {



    function __construct() {
	@session_start();
       $this->view = new View();
       try{
       $accountObject = new accountModel();
       if (@$_SESSION['uid']){
       $this->view->isPasswordChanged  = $accountObject->isPasswordChanged();
       }
       }catch(Exceptions $e){
       	print_r($e);
       }
    }



}
?>