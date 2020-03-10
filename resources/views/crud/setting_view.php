<?php 
$this->data = (object)$this->data; 
$this->data->layout = (object)$this->data->layout;
$this->data->sidebar = (object)$this->data->sidebar;
$this->section("admin_header");
?>
<div class="container">
    <div class="row">
        <div class="<?php echo ($this->data->layout->sidebar == true ? 'col-md-3':'hide');?>">
            <?php $this->section("sidebar.crud")?>
        </div>
        <div class="<?php echo ($this->data->layout->sidebar == true ? 'col-md-9':"col-md-12");?>">
            <div class="card table-wrapper">
                <div class="card-body">
                    <?php 
                        //header
                        $this->data->header = (object) $this->data->header;
                        $this->data->header->actions = (object) $this->data->header->actions;
                        
                        if ($this->data->header->actions){
                            echo "<div class='pull-right'>";
                            echo "<ul class='header-nav'>";
                                foreach($this->data->header->actions as $action){
                                    $action = (object) $action;
                                    echo "<li><a href='".$action->url."'><i class='".$action->icon."' ></i> ".$action->name."</a></li>";
                                }
                            echo "</ul>";
                            echo "</div>";
                        }
                        if ($this->data->header){
                            echo "<h2 class='table-title'>".$this->data->header->title."</h2>";
                        }
                        
                    ?>
            <?php
                if ($this->message){
                    echo "<div class='alert alert-info'>".$this->message."</div>";
                }
                //data display
                if (count($this->data->datas) >= 1){
                    if (count($this->data->table_style->custom_attributes)>=1){
                        foreach($this->data->table_style['custom_attributes'] as $key=> $attr){
                            $custom_attributes .= $key."=\"".$attr."\" "; 
                        }
                    }
                    if (isset($this->data->table_style['class'])){
                        $custom_classes = $this->data->table_style['class'];
                    }
                    if (isset($this->data->table_style['id'])){
                        $custom_id = $this->data->table_style['id'];
                    }
                    echo "<table class='table ".$custom_classes."' id=\"".$custom_id."\" ".$custom_attributes.">";
                    echo "<tr>";
                    foreach($this->data->labels as $label){
                        echo "<th>".$label."</th>";
                    }
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    
                    foreach($this->data->datas as $data){
                        echo "<tr>";
                            foreach($data as $cell){
                                echo "<td>".$cell."</td>";
                                
                            }
                            echo "<td>";
                                $UpdateURI = "";
                                $URI = $_SERVER['REQUEST_URI'];
                                $ex = explode("/",$URI);
                                unset($ex[count($ex)-1]);
                                $UpdateURI = implode("/",$ex)."/update?id=".$data[0]['id'];
                                $RemoveURI = implode("/",$ex)."/delete?id=".$data[0]['id'];
                                if ($this->data->actions['edit'] == true){
                                    echo "<a href='".$UpdateURI."'>Edit</a> | ";
                                }
                                if ($this->data->actions['remove'] == true){
                                    echo "<a href='".$RemoveURI."' onclick=\"return confirm('Are you sure you want to delete?')\">Remove</a>";
                                }
                            echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }else{
                    echo "<div class='alert alert-info'>No Records Found!</div>";
                }
            ?>
            </div>
            </div>
        </div>
    </div>
</div>
<?php $this->section("admin_footer");?>