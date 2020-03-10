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
                      
                        if ($this->message){
                            echo "<div class='alert alert-info'>".$this->message."</div>";
                        }
                        if ($this->data->form){
                            $this->data->form = (object) $this->data->form;
                            $this->data->form->layout = (object) $this->data->form->layout;
                            echo "<div class='".$this->data->form->layout->class."'>";
                            echo "<form action='".$this->data->form->action."' enctype='".$this->data->form->enctype."' method='".$this->data->form->method."' class='".$this->data->form->class."' id='".$this->data->form->id."'>";
                            foreach($this->data->form->fields as $field){
                                $field = (object) $field;
                                if ($field->type == 'hidden'){
                                    echo "<div class='form-group'>
                                            <input type='hidden' 
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ".($field->required == true ? "required":"")."
                                                />
                                          </div>";
                                }
                                if ($field->type == 'text'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='text' 
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ".($field->required == true ? "required":"")."
                                                />
                                          </div>";
                                }
                                if ($field->type == 'email'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='email' 
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ".($field->required == true ? "required":"")."
                                                />
                                          </div>";
                                }
                                if ($field->type == 'file'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='file' 
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ".($field->required == true ? "required":"")."
                                                />
                                          </div>";
                                }
                                if ($field->type == 'password'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='password' 
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ".($field->required == true ? "required":"")."
                                                />
                                          </div>";
                                }
                                if ($field->type == 'select'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>";
                                            echo "<select 
                                                        ".($field->required == true ? "required":"")."
                                                        name='".$field->name."' 
                                                        class='form-control ".$field->class."' 
                                                        id='".$field->id."'>";
                                                        echo "<option alue=''>".$field->placeholder."</option>";
                                            foreach($field->items as $i){
                                                $i = (object) $i;
                                                echo "<option value='".$i->value."' ".($i->default ? "SELECTED":"").">".$i->name."</option>";
                                            }  
                                            echo "</select>";
                                    echo "</div>";
                                }
                                
                                if ($field->type == 'checkbox'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='checkbox' 
                                                ".($field->required == true ? "required":"")."
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                />
                                          </div>";
                                }
                                if ($field->type == 'radio'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <input type='radio' 
                                                ".($field->required == true ? "required":"")."
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                />
                                          </div>";
                                }
                                if ($field->type == 'image'){
                                    echo "<div class='form-group'>
                                            <img 
                                                class='thumbnail ".$field->class."' 
                                                id='".$field->id."' 
                                                src='".$field->value."'
                                                />
                                          </div>";
                                }
                                if ($field->type == 'textarea'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label.($field->required == true?"*":"")."</label>
                                            <textarea 
                                                ".($field->required == true ? "required":"")."
                                                name='".$field->name."' 
                                                class='form-control ".$field->class."' 
                                                id='".$field->id."' 
                                                placeholder='".$field->placeholder."' 
                                                value='".$field->value."'
                                                ></textarea>
                                          </div>";
                                }
                                if ($field->type == 'button'){
                                    echo "<div class='form-group'>
                                            <label>".$field->label."</label>
                                            <input type='button' 
                                                name='".$field->name."' 
                                                class='form-control btn ".$field->class."' 
                                                id='".$field->id."' 
                                                value='".$field->value."'
                                                />
                                          </div>";
                                }
                                if ($field->type == 'submit'){
                                    echo "<div class='form-group'>
                                            
                                            <input type='submit' 
                                                name='".$field->name."' 
                                                class='form-control btn ".$field->class."' 
                                                id='".$field->id."' 
                                                value='".$field->value."'
                                                />
                                          </div>";
                                }
                                if ($field->type == "richTextEditor"){
                                    echo "<div class='form-group'>";
                                        echo "<label>".$field->label."</label>";
                                        echo "<textarea name='".$field->name."' class='".$field->class."' id='".$field->id."' >".$field->value."</textarea>";
                                    echo "</div>";
                                    echo "<script>window.onload=function(){
                                        CKEDITOR.replace( '".$field->name."' );
                                        };</script>";
                                }
                            }
                            echo "</form>";
                            echo "</div>";
                            echo "<div style='clear:both;'></div>";
                        }
                    ?>
                    
            </div>
            </div>
        </div>
    </div>
</div>
<?php $this->section("admin_footer");?>
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
