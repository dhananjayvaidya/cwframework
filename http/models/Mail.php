<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'core/libs/PHPMailer/src/Exception.php';
require 'core/libs/PHPMailer/src/PHPMailer.php';
require 'core/libs/PHPMailer/src/SMTP.php';
require 'core/libs/PHPMailer/src/POP3.php';
require 'core/libs/PHPMailer/src/OAuth.php';

class MailModel extends DB{
    public $TableName = "mails";
    public $Schema = array(
        'id'            => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "primary_key" => true,
            'auto_increment' => true
        ),
        "mail_to" => array(
            "dataType" => "string",
            "dataSize" => 255,
            "not_null" => true
        ),
        "mail_from" => array(
            "dataType" => "string",
            "dataSize" => 255,
            "not_null" => true
        ),
        "mail_subject" => array(
            "dataType" => "string",
            "dataSize" => 255,
            "not_null" => true
        ),
        "mail_message" => array(
            "dataType" => "text",
            "not_null" => true
        ),
        "timestamp" => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        "mod_timestamp" => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        ),
        "etms"          => array(
            "dataType"=>"int", 
            "dataSize"=>11, 
            "not_null"=>true,
            "default" => 0
        )
    );
    function __construct(){
        parent::__construct($this);
    }
    function send($data){
        //add log
        $this->add(array(
            "mail_to"           => $data['to'] ,
            "mail_from"         => $data['from'],
            "mail_subject"      => $data['subject'],
            "mail_message"      => $data['message'],
            "timestamp"         => time(),
            "mod_timestamp"     => time()
        ));
        
        //send mail 
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                       // Enable verbose debug output
            //$mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = _MAIL_SERVER_;  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = _MAIL_USERNAME_;                     // SMTP username
            $mail->Password   = _MAIL_PASSWORD_;                               // SMTP password
            $mail->SMTPSecure = _MAIL_SMTP_SECURE_;                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = _MAIL_PORT_;                                      // TCP port to connect to
        
            //Recipients
            $mail->setFrom($data['from']);
            $mail->addAddress($data['to']);     // Add a recipient
            $mail->addAddress('dhananjay@zitasoftinfotech.com');               // Name is optional
            
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['message'];
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>