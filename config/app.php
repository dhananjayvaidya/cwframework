<?php
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/

//development 
define("_DEV_",false);
/*
 * @Load Database config 
*/
require "config/database.php";

define("_PATH_",base_url());	     //Global Path
define("_COMPANY_NAME_","CWFramework");		 //company name 
//setting time zone
date_default_timezone_set("Asia/Kolkata");		     //default time zone
define("_ADMIN_PATH_",_PATH_."admin");
define("_API_PATH_",_PATH_."api/");
define("_CONTACT_NUMBER_","+91-1234567890");
//define("_CONTACT_NUMBER_2","+91-9999999999");
define("_EMAIL_ID_","info@cwframework.com");

define("_SYS_LANG_","en");
/*
* @Mail Settings
*/
define("_MAIL_USERNAME_" , "");
define("_MAIL_PASSWORD_","");
define("_MAIL_SERVER_","");
define("_MAIL_PORT_","465");
define("_MAIL_SMTP_SECURE_","SSL");
define("_MAIL_USER_DISPLAY_NAME_","CWFramework");

/*
* reCaptcha Settings
*/
define("_RECAPTCHA_SITE_KEY_","---KEY---");
define("_RECAPTCHA_SITE_SECRET_","---SECRET---");
/*
 * @URL & SEO
*/
require "config/routes.php";
?>