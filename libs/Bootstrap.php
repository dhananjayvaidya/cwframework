<?php
class Bootstrap {
    function __construct() {
        if (!$_GET){
            require "http/controllers/home.php";
            $controller = new Index();
            $controller->first();
            return false;
        }
        //get the url 
        $url = $_GET['url'];
        //trim the url
        $url = rtrim($url, "/");
        //explode the $url 
        $url = explode("/", $url);
        //check for the null url 
        //require the specify controller
        $file = "http/controllers/" . $url[0] . ".php";
        if (file_exists($file) == true){
            require $file;
           //invoke the controller
            $controller = new $url[0]();
            if ($url[0] !== "account"){
            //setup the modelpath
            $ModelPath = "http/models/".$url[0].".php";
            }
            //include the model if exists
            if (@file_exists($ModelPath) == true){
                require $ModelPath;
            }
            
        }else{
            require 'http/controllers/error.php';
            $controller = new Error();
            return false;
        }
        if (count($url) >= 2) {
            if (method_exists($controller, $url[1])){
            //look around for the method and the parameters
            if (count($url) == 2) {
                //we have to call the method with no parameters
                $controller->$url[1]();
            } else {
                //unset the url 
                $method = $url[1];
                unset($url[0],$url[1]);
                
                $controller->$method($url);
            }
            }else{
                require 'http/controllers/error.php';
                $controller = new Error();
                return false;
            }
        }else{
            $controller->first();
        }
    }
}
?>
                            
                            
                            
                            
                            