<?php 
//redefine object
$this->data->sidebar->menu_items = (object) $this->data->sidebar->menu_items;
if ($this->data->sidebar->title){
    echo "<h1>".$this->data->sidebar->title."</h1>";
}
if ($this->data->sidebar->menu_items){
    echo "<div class='left-menu'>";
    echo "<ul class='nav'>";
        foreach($this->data->sidebar->menu_items as $items){
            $items = (object) $items;
            echo "<li><a href='".$items->url."'><i class='".$action->icon."' ></i> ".$items->name."</a></li>";
        }
    echo "</ul>";
    echo "</div>";
}
?>