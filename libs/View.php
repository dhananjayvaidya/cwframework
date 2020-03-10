<?php
/**
 * Description of View
 *
 * @author dhananjay vaidya
 */

class View {

    public $c;
    //put your code here
    public function __construct() {

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

}



?>