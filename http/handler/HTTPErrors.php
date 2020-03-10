<?php 
class HTTPErrors extends Controller{
   
    function notFound(){
        $this->view->errors("404");
    }
    function overLoad(){
        
    }
    function runTimeError($message){
        $this->view->message = $message;
        $this->view->errors("runTimeError");
    }
}
?>