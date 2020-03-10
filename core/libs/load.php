<?php 
/*
* @Author   : Dhananjay Vaidya
* @Version  : 3.0
*/
$scandir  = scandir("./libs/extended/");

unset($scandir[0],$scandir[1]);

foreach($scandir as $d){
	if (is_file("./libs/extended/".$d)){
		require "./libs/extended/".$d;
	}
}
?>