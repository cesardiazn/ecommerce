<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;


$app->get("/admin/forgot", function() {
	
	
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
		]);
		
	$page->setTpl("forgot");
	exit;
});


$app->post("/admin/forgot", function() {
	
	// Envio de email con el link de recuperacion del password
	$user = User::getForgot($_POST["email"]);
	
	// redireccionamiento a la pag de confirmacion del envio del codigo de recuperacion
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){
	
	// direccionamiento a la pag de confirmacion del envio del codigo de recuperacion
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
			]);
			
		$page->setTpl("forgot-sent");
	
});


$app->get("/admin/forgot/reset", function(){
	
	// validar si el codigo de reset es valido
	$user = User::validForgotDecrypt($_GET["code"]);

	//echo "<script>console.log('desperson :".$user["desperson"]."');</script>";
	//echo "<script>console.log('code 00 :".$_GET["code"]."');</script>";
	// direccionamiento a la pag de confirmacion del envio del codigo de recuperacion
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
			]);
			
		$page->setTpl("forgot-reset", array(
			"name"=>$user["desperson"],
			"code"=>$_GET["code"]
		));
		
});


$app->post("/admin/forgot/reset", function(){
	
	// validar si el codigo de reset es valido
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	// vamos a encriptar el password para ser grabado
	// con password_hash despues podemos verificar con password_verify() 
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$user->setPassword($password);


	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
		]);
		
	$page->setTpl("forgot-reset-success");

	
});


?>