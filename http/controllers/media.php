<?php 
require "http/models/Media.php";
class media extends Controller{
    function __construct(){
        parent::__construct();
    }
    public function image($e){
        $MediaObj = new MediaModel();
        $MediaData = $MediaObj->getById($e[0]);
        //print_r($MediaData);
        header("Content-Type: image/png");
        $FileLocation = $MediaData[0]['file_location'];
        $FileLocation = explode("/",$FileLocation);
        $FileName = end($FileLocation);
        unset($FileLocation[count($FileLocation)-1]);
        $FileLocation = implode("/",$FileLocation);
        $Loc = $FileLocation.($_GET['size']?"/".$_GET['size']:"")."/".$FileName;
        $f = file_get_contents($Loc);
        echo $f;
        //print_r(fread($f));
    }
}
?>