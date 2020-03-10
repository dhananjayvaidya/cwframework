<?php 
require "http/models/User.php";
class user_admin extends Controller{
    function __construct(){
        parent::__construct();
    }
    public function add(){
        if ($_POST){
            $UserObj = new UserModel();
            
            $username       = $_POST['username'];
            $password       = $_POST['password'];
            $parent_id      = $_POST['parent_id'];
            $role           = $_POST['role'];
            
            $data = array(
                "username"      => $username,
                "password"      => $password,
                "parent_id"     => $parent_id,
                "role"          => $role,
                "timestamp"     => time(),
                "mod_timestamp" => time()
            );
            if ($UserObj->add($data)){
                $this->view->message = "Successfully Added User.";
            }else{
                $this->view->message = "Problem Occur ";
            }
            
        }
        $this->view->title = "Add User | "._COMPANY_NAME_;
        $this->view->data = array(
                // "layout" => array(
                //         "sidebar" => true,
                        
                //     ),
                // "sidebar"   => array(
                //         "title" => "Projects",
                //         "menu_items"=>array(
                                
                //                 array(
                //                     "name" => "Projects",
                //                     "url"  => _ADMIN_PATH_."/project/view"
                //                 ),
                //                 array(
                //                     "name" => "Project Category",
                //                     "url"  => _ADMIN_PATH_."/project/category/view"
                //                 ),
                //                 array(
                //                     "name" => "Projects Features",
                //                     "url"  => _ADMIN_PATH_."/project/feature/view"
                //                 ),
                //             )
                //     ),
                "header" => array(
                        "title" => "Add User",
                        "actions" => array(
                                
                                array(
                                        "name" => "View",
                                        "url"  => _ADMIN_PATH_."/user/view",
                                        "icon" => "fa fa-list"
                                )
                            )
                    ),
                "form"   => array(
                        "layout" => array(
                                "class" => "col-md-12"
                            ),
                        "fields" => array(
                                array(
                                        "type"          => "text",
                                        "label"         => "Username",
                                        "placeholder"   => "Username",
                                        "value"         => "",
                                        "name"          => "username"
                                    ),
                                array(
                                        "type"          => "password",
                                        "label"         => "Password",
                                        "placeholder"   => "Password",
                                        "value"         => "",
                                        "name"          => "password"
                                    ),
                                array(
                                        "type"          => "text",
                                        "label"         => "Parent Id",
                                        "placeholder"   => "Parent Id",
                                        "value"         => "",
                                        "name"          => "parent_id"
                                    ),
                                array(
                                        "type"          => "select",
                                        "label"         => "Role",
                                        "placeholder"   => "Role",
                                        "value"         => array(
                                                                array(
                                                                    "name" => "Suadmin",
                                                                    "value" => 1
                                                                ),
                                                                array(
                                                                    "name" => "User",
                                                                    "value" => 2
                                                                ),
                                                            ),
                                        "name"          => "role"
                                    ),
                                array(
                                        "type" => "submit",
                                        "class" => "btn-primary",
                                        "value" => "Submit"
                                    )
                            ),
                        "url"    => "",
                        "method" => "post",
                        "enctype"=> "multipart/form-data"
                        
                    )
            );
        $this->view->render("crud/form");
    }
    public function update(){
        $UserObj = new UserModel();
        if ($_POST){
            
            
            $username       = $_POST['username'];
            $password       = $_POST['password'];
            $parent_id      = $_POST['parent_id'];
            $role           = $_POST['role'];
            
            $data = array(
                "username"      => $username,
                "password"      => $password,
                "parent_id"     => $parent_id,
                "role"          => $role,
                "timestamp"     => time(),
                "mod_timestamp" => time()
            );

            if ($UserObj->update($data,$_GET['id'])){
                $this->view->message = "Successfully Updated User.";
            }else{
                $this->view->message = "Problem Occur.";
            }
        }
        $ClientData = $UserObj->getById($_GET['id']);
        //print_r($ClientData);
        $Id             = $ClientData[0]['id'];
        $Username       = $ClientData[0]['username'];
        $Password       = $ClientData[0]['password'];
        $ParentId       = $ClientData[0]['parent_id'];
        $RoleId         = $ClientData[0]['role'];

        $this->view->title = "Update User | "._COMPANY_NAME_;
        $this->view->data = array(
                // "layout" => array(
                //         "sidebar" => true,
                        
                //     ),
                // "sidebar"   => array(
                //         "title" => "Projects",
                //         "menu_items"=>array(
                                
                //                 array(
                //                     "name" => "Projects",
                //                     "url"  => _ADMIN_PATH_."/project/view"
                //                 ),
                //                 array(
                //                     "name" => "Project Category",
                //                     "url"  => _ADMIN_PATH_."/project/category/view"
                //                 ),
                //                 array(
                //                     "name" => "Projects Features",
                //                     "url"  => _ADMIN_PATH_."/project/feature/view"
                //                 ),
                //             )
                //     ),
                "header" => array(
                        "title" => "Update User",
                        "actions" => array(
                                array(
                                        "name" => "Add",
                                        "url"  => _ADMIN_PATH_."/user/add",
                                        "icon" => "fa fa-plus"
                                ),
                                array(
                                        "name" => "View",
                                        "url"  => _ADMIN_PATH_."/user/view",
                                        "icon" => "fa fa-list"
                                )
                            )
                    ),
                "form"   => array(
                        "layout" => array(
                                "class" => "col-md-12"
                            ),
                        "fields" => array(
                                array(
                                        "type"          => "hidden",
                                        "value"         => $Id,
                                        "name"          => "id",
                                        "required"      => true
                                    ),
                                array(
                                        "type"          => "text",
                                        "label"         => "Username",
                                        "placeholder"   => "Username",
                                        "value"         => $Username,
                                        "name"          => "username",
                                        "required"      => true
                                    ),
                                
                                array(
                                        "type" => "submit",
                                        "class" => "btn-primary",
                                        "value" => "Update User"
                                    )
                            ),
                        "url"    => "",
                        "method" => "post",
                        "enctype"=> "multipart/form-data"
                        
                    )
            );
        $this->view->render("crud/form");
    }
    public function delete(){
        if ($_GET['id']){
            $UserObj = new UserModel();
            if ($UserObj->remove($_GET['id'])){
                $this->view->message = "Successfully Removed";
            }else{
                $this->view->message = "Problem Occur while Removing the User.";
            }
        }
        $this->view();
    }
    public function view(){
        $UserObj = new UserModel();
        $dataSet = array();
        $d = $UserObj->getAll();
        foreach($d as $key=>$val){
            $dataSet[] = array($val['id'], $val['username'], date('d-M-Y',$val['mod_timestamp']));
        }
        $this->view->title = "Users | "._COMPANY_NAME_;
        $this->view->data = array(
                // "layout" => array(
                //         "sidebar" => true,
                //     ),
                // "sidebar"   => array(
                //         "title" => "Projects",
                //         "menu_items"=>array(
                                
                //                 array(
                //                     "name" => "Projects",
                //                     "url"  => _ADMIN_PATH_."/project/view"
                //                 ),
                //                 array(
                //                     "name" => "Project Category",
                //                     "url"  => _ADMIN_PATH_."/project/category/view"
                //                 ),
                //                 array(
                //                     "name" => "Projects Features",
                //                     "url"  => _ADMIN_PATH_."/project/feature/view"
                //                 ),
                //             )
                //     ),
                "header" => array(
                        "title" => "View Clients",
                        "actions" => array(
                                array(
                                        "name" => "Add",
                                        "url"  => _ADMIN_PATH_."/user/add",
                                        "icon" => "fa fa-plus"
                                    ),
                            )
                    ),
                "table_style"=>array(
                        "class"=>"table",
                        "id" => "table_id",
                        "custom_attributes" => array(
                                "a"=>"1",
                                "b"=>"3",
                        )
                    ),
                "actions"=>array(
                        "edit"=>true,
                        "remove"=>true
                    ),
                "labels" => array(
                        "id",
                        "Username",
                        "created on"
                    ),
                "datas" => $dataSet
            );
        $this->view->render("crud/view");
    }
}
?>