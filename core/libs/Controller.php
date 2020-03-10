<?php
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
//@require "models/account.php";
require "core/models/CWFLanguage.php";
require "core/libs/CWFSecurity.php";
class Controller {
    function __construct() {
	    @session_start();
        $this->view = new View();
        $this->setting = new SettingsModel();
        
        
    }
    


}
?>