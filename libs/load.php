<?php 
$scandir  = scandir("./libs/extended/");

unset($scandir[0],$scandir[1]);

foreach($scandir as $d){
	if (is_file("./libs/extended/".$d)){
		require "./libs/extended/".$d;
	}
}
?>