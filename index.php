<?php 
session_start();

// define las dependencias
require_once("vendor/autoload.php");

// name space
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

// $app = new \Slim\Slim();
use \Slim\Slim;
$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new Page();
	
	$page->setTpl("index");

});


// definimos una ruta para la pagina administrativa
$app->get('/admin', function() {

	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new PageAdmin();
	
	$page->setTpl("index");

});


// definimos una ruta para la pagina de login
$app->get('/admin/login', function() {

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	// como el contructor carga header y footer, vamos a definir un parametro que permita deshabilitarlo
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("login");

});


// definimos una ruta, para cuando recibimos un metodo post de la pag login
$app->post('/admin/login', function() {
	
	User::login($_POST["login"], $_POST["password"]);
	
	header("Location: /admin");
	
	exit;
});

$app->get('/admin/logout', function() {
	User::logout();
	
	header("Location: /admin/login");
	exit;
});

$app->run();



	/*
	$sql = new Hcode\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	
	echo "<pre>". json_encode($results, JSON_PRETTY_PRINT) . "</pre>";
	*/

 ?>