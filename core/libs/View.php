<?php
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/

class View {

    public $c;
    //put your code here
    public function __construct() {
        $this->setting = new SettingsModel();
    }
    /*
    * General view port
    */
    public function render($name){
        $file = "resources/views/".$name.".php";
        if (file_exists($file)){
            @require $file;
        }else{
            //HTTPErrors::runTimeError("View : ".$file." not found ");
        }
    }
    
    /*
    * @section for page
    * this function call will load the different sections on the page
    */
    public function section($sectionName){
        $file = "resources/sections/".$sectionName.".php";
        if (file_exists($file)){
            @require $file;
        }else{
           // HTTPErrors::runTimeError("View->section : ".$file." not found ");
        }
    }
    /*
    * Error handler view port 
    */
    public function errors($e){
        $file = "resources/errors/".$e.".php";
        if (file_exists($file)){
            @require $file;
        }else{
            //HTTPErrors::runTimeError("View->errors : ".$file." not found ");
        }
    }
    
    /*
    * @response
    */
    public function response($data){
        header("Content-Type: application/json");
        echo json_encode($data);
    }
    function l($s){
        $CWFLanguageObj = new CWFLanguageModel();
        if ($CWFLanguageObj->checkDefination($s) == false){
            //insert 
            $CWFLanguageObj->add(array(
                    "lang_def"      => $s,
                    "en"            => str_replace("_"," ",$s),
                    "timestamp"     => time(),
                    "mod_timestamp" => time()
                ));
            return str_replace("_"," ",$s);
        }else{
            //get
            $data = $CWFLanguageObj->getByLangDef($s);
            return ucwords($data[0][_SYS_LANG_]);
        }
    }
}



?>