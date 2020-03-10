<?php

class home extends Controller{
    function __construct() {
		parent::__construct();
    }
    function first(){
        
			$this->view->title="Welcome to "._COMPANY_NAME_;
			$this->view->render("front/index/index");
			
		
	}
}
?>