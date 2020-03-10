<?php
ob_start();
session_start();

error_reporting(0);
require "core/tools/Tools.php";
require "core/libs/DB.php";
require "http/models/Settings.php";
require 'core/libs/Controller.php';
require "core/libs/View.php";
require "core/libs/Route.php";
require 'config/app.php';
$App = new Route();


ob_flush();
?>