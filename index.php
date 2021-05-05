<?php 
session_start();

// define las dependencias
require_once("vendor/autoload.php");

// name space
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

// $app = new \Slim\Slim();
$app = new Slim();

$app->config('debug', true);


require_once("functions.php");
/*
Comenzamos a definir las rutas de acceso
para facilitar el mantenimiento las distribuimos en diferentes archivos, de forma clasificada
debemos tomar en cuenta el orden en que se incluyen, para que no afecte su funcionamiento
*/
require_once("route_site.php");
require_once("route_admin.php");
require_once("route_admin_login.php");
require_once("route_admin_users.php");
require_once("route_admin_categories.php");
require_once("route_admin_products.php");
require_once("route_admin_forgot.php");




$app->run();



	/*
	$sql = new Hcode\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	
	echo "<pre>". json_encode($results, JSON_PRETTY_PRINT) . "</pre>";
	*/

 ?>