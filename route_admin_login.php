<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;


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



?>