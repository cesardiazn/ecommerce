<?php 

// define las dependencias
require_once("vendor/autoload.php");

// name space
use \Hcode\Page;

// $app = new \Slim\Slim();
use \Slim\Slim;
$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	// echo "OK";
	
	/*
	$sql = new Hcode\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	
	echo "<pre>". json_encode($results, JSON_PRETTY_PRINT) . "</pre>";
	*/
	
	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new Page();
	
	$page->setTpl("index");

});

$app->run();

 ?>