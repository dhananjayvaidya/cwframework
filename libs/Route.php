<?php 
require "http/handler/HTTPErrors.php";
class Route {
    /*
    * @error object 
    */
    public $HTTPErrors;
    public $found;
    function __construct(){
        $this->HTTPErrors =  new HTTPErrors();
    }
    /*
    * @usage : $Route->get("login","controllerName->methodName")
    *        : $Route->get("product/get/{%d1}/{%d1}","controllerName->methodName@%d1,%d2");
    *        : $Route->get("profile/{%d}","controllerName->methodName@%d");
    */
     function get($route,$call){
        if ($_SERVER['REQUEST_METHOD'] == "GET" || $_SERVER['REQUEST_METHOD'] == "get"){
        //url filter 
        @$url = "/".$_GET['url'] ;
        if (strpos($route,"{}",0) >= 1){
            $e = explode("/",$url);
            $route = str_replace("{}","",$route);
            $r = explode("/",$route);
            $newpath = array_unique(array_merge($e,$r));
            $newRoute = array();
            foreach($newpath as $t){ $newRoute[] = $t; }
            $newRoute =  implode("/",$newRoute);
            //strip parameters
            $g = explode("{}",$route);
            $te =explode($g[0],$newRoute);
            //replace / to @
            $p = str_replace("/","@",$te[1]); 
            $p = "@".$p;
        }else{
            $newRoute = $route;
        }
            if ($url == $newRoute  || $url == "/"){
                
                $this->found = 1;
            //evalute the $call
            $explode = explode("->",$call);
            $controller_name = $explode[0];
            $function_name   = $explode[1].$p;
            //print_r($explode);
            //include the controller
            $file = "http/controllers/".$controller_name.".php";
                if (file_exists($file) == true){
                    require $file;
                    //check for parameters
                    $f = explode("@",$function_name);
                    @$params = explode(",",$f[1]);
                    
                    if (count($params) >= 1){ //parameterized call to function
                        //has parameters
                        $c = new $controller_name();
                        $r = (string)$f[0];
						$c->$r($params);
                    }else{ //non parameterized call to function
                        $c = new $controller_name();
                        $c->$function_name();
                    }
                    die();
                }else{
                    //load error class
                    $this->HTTPErrors->runTimeError("Controller Path : ".$file." Not found !");            
                }
            }else{
               $this->found = 0;
                    
            }
        }else{
            $this->found = 0;
        }
    }
    /*
    * @usage : $Route->post("login","controllerName->methodName")
    *        : $Route->post("profile/{%d}","controllerName->methodName@%d");
    */
     function post($route,$call){
        if ($_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "post"){
        //url filter 
        @$url = "/".$_GET['url'] ;
        if (strpos($route,"{}",0) >= 1){
            $e = explode("/",$url);
            $route = str_replace("{}","",$route);
            $r = explode("/",$route);
            $newpath = array_unique(array_merge($e,$r));
            $newRoute = array();
            foreach($newpath as $t){ $newRoute[] = $t; }
            $newRoute =  implode("/",$newRoute);
            //strip parameters
            $g = explode("{}",$route);
            $te =explode($g[0],$newRoute);
            //replace / to @
            $p = str_replace("/","@",$te[1]); 
            $p = "@".$p;
        }else{
            $newRoute = $route;
        }
            if ($url == $newRoute  || $url == "/"){
                
                $this->found = 1;
            //evalute the $call
            $explode = explode("->",$call);
            $controller_name = $explode[0];
            $function_name   = $explode[1].$p;
            //print_r($explode);
            //include the controller
            $file = "http/controllers/".$controller_name.".php";
                if (file_exists($file) == true){
                    require $file;
                    //check for parameters
                    $f = explode("@",$function_name);
                    @$params = explode(",",$f[1]);
                    
                    if (count($params) >= 1){ //parameterized call to function
                        //has parameters
                        $c = new $controller_name();
                        $t = (string)$f[0];
						$c->$t($params);
                    }else{ //non parameterized call to function
                        $c = new $controller_name();
                        $c->$function_name();
                    }
                    die();
                }else{
                    //load error class
                    $this->HTTPErrors->runTimeError("Controller Path : ".$file." Not found !");            
                }
            }else{
               $this->found = 0;
                    
            }
        }else{
            $this->found = 0;
        }
    }
    function crud($route,$controller_name){
        @$url = "/".$_GET['url'] ;
        if (strpos($route,"{}",0) >= 1){
            $e = explode("/",$url);
            $route = str_replace("{}","",$route);
            $r = explode("/",$route);
            $newpath = array_unique(array_merge($e,$r));
            $newRoute = array();
            foreach($newpath as $t){ $newRoute[] = $t; }
            $newRoute =  implode("/",$newRoute);
            //strip parameters
            $g = explode("{}",$route);
            $te =explode($g[0],$newRoute);
            //replace / to @
            $p = str_replace("/","@",$te[1]); 
            $p = "@".$p;
        }else{
            $newRoute = $route;
        }
        $URLCore = explode("/",$url);
        $CRUD_OPR = end($URLCore);
        $end = count($URLCore)-1;
        unset($URLCore[$end]);
        $URLCore = implode("/",$URLCore); 
        if ($URLCore == $newRoute){
            $file = "http/controllers/".$controller_name.".php";
            if (file_exists($file) == true){
                require $file;
            }else{
                //load error class
                $this->HTTPErrors->runTimeError("Controller Path : ".$file." Not found !");            
            }
            switch ($CRUD_OPR){
                case "add":
                    $c = new $controller_name();
                    $c->add();
                    die();
                break;
                case "update":
                    $c = new $controller_name();
                    $c->update();
                die();
                break;
                case "delete":
                    $c = new $controller_name();
                    $c->delete();
                    die();
                break;
                case "view":
                    $c = new $controller_name();
                    $c->view();
                    die();
                break;
            }
            echo "Yes We got it, & CRUD Operation is ".$CRUD_OPR;   
        }else{
            echo "<br>Invalid Call";
        }
        
    }
    function error(){
        if ($this->found !== 1){
            $this->HTTPErrors->notFound();
        }
    }
}
?>