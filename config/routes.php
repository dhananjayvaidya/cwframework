<?php 
/*
* @invoke Route class 
*/
$App = new Route(); //dont remove this line. 

/*
 * $App->post("/account/login","account->login"); // post method routes
 * $App->get("/products/get/{}","products->getById"); // the variable which is in this `{}` will automatically send to getById method
 * $App->get("/products/get/{}","products->get@productType"); //here you can forge the parameter if required
 * $App->get("/products/view","products->get@mobile");  
*/

/**
 * @Web Routes
 */

$App->get("/","home->first"); //default call

/**
 * @Admin Routes
 */
$App->get("/admin","admin->first");
$App->post("/admin","admin->first");
$App->get("/admin/logout","admin->logout");
$App->crud("/admin/user","user_admin");
/*
* You can add other routes below.
*/
$App->crud("/api/user","user_service");
/*
* Common Routes
*/
$App->get("/media/img/{}","media->image");

/*
* Page Not Found Route
* NOTE : DONOT REMOVE THIS ROUTE, IT IS FOR PAGE NOT FOUND ERROR.
* IF YOU WANT TO SHOW BLANK SCREEN THEN YOU CAN REMOVE IT.
*/
$App->error();

?>