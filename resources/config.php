<?php 

//turn on output buffering
ob_start();

//start a session
session_start();
//session_destroy();

//if not defined apply null, if it is not define it as DS. DS will be our forward slash or backslash depending on system
defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);

//define the template front to point to our front folder
defined("TEMPLATE_FRONT") ? null : define("TEMPLATE_FRONT", __DIR__ . DS . "templates/front");

//define the back
defined("TEMPLATE_BACK") ? null : define("TEMPLATE_BACK", __DIR__ . DS . "templates/back");

//define the image directory
defined("UPLOAD_DIRECTORY") ? null : define("UPLOAD_DIRECTORY", __DIR__ . DS . "uploads");

//define database constants
defined("DB_HOST") ? null : define("DB_HOST", "localhost");
defined("DB_USER") ? null : define("DB_USER", "root");
defined("DB_PASS") ? null : define("DB_PASS", "root");
defined("DB_NAME") ? null : define("DB_NAME", "ecom_db");

//create a connection variable
$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

//include the functions file
require_once("functions.php");
require_once("cart.php");
?>