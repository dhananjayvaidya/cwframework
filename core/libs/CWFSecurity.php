<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
class CWFSecurity{
    function __construct(){
        
    }
    function validateRequest(){
        // Google reCaptcha secret key
        // $secretKey  = _RECAPTCHA_SITE_SECRET_;
        
        // $statusMsg = '';
        // if(isset($_POST['captcha_response']) && !empty($_POST['captcha_response'])){
        //     // Get verify response data
        //     $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['captcha_response']);
        //     $responseData = json_decode($verifyResponse);
        //     //print_r($responseData);
        //     if($responseData->success){
        //         //Contact form submission code goes here ...
      
        //         //$statusMsg = 'Your contact request have submitted successfully.';
        //     }else{
        //         //$statusMsg = 'Robot verification failed, please try again.';
        //         echo json_encode(array("code"=>500,"message"=>"Bot activity detect, issue reported."));
        //         //header("Location: "._PATH_."botdetected");
        //         die();
        //     }
        // }
        
    }
}
?>