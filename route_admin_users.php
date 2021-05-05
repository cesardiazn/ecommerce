<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get("/admin/users", function(){
	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();

	$users = User::listAll();
	
	$page = new PageAdmin();
		
	$page->setTpl("users", array(
		"users"=>$users
		));
});

$app->get("/admin/users/create", function(){
	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();

	$page = new PageAdmin();
		
	$page->setTpl("users-create"); 
	// el parametro "user-create" se refiere al archivo html que contiene el template
});


// ruta para eliminar los datos, mantiene el mismo nombre que la de crear, pero se agrega delete en la ruta
$app->get("/admin/users/:iduser/delete", function($iduser){

	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();
	
	$user = new User();
	
	$user->get((int)$iduser);
	
	$user->delete();
	
	header("Location: /admin/users");
	exit;	
	
});

// ruta para actualiza usuario
$app->get("/admin/users/:iduser", function($iduser){
	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();

	$user = new User();
	
	$user->get((int)$iduser);
	
	$page = new PageAdmin();
		
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
	// el parametro "user-update" se refiere al archivo html que contiene el template
});


// ruta para salvar los datos, mantiene el mismo nombre que la de crear, pero el metodo es post
$app->post("/admin/users/create", function(){

	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();
	//var_dump($_POST);
	$user = new User();
	
	// si inadmin no es seleccionado no tiene valor por lo que no existe en el Post, verificamos y le asignamos valor
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	
	$user->setData($_POST);
	
	$user->save();
	
	header("Location: /admin/users");
	exit;
	
});


// ruta para salvar los datos, mantiene el mismo nombre que la de crear, pero el metodo es post
$app->post("/admin/users/:iduser", function($iduser){

	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();
	
	$user = new User();
	
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	
	$user->get((int)$iduser);
	
	$user->SetData($_POST);
	
	$user->update();
	
	header("Location: /admin/users");
	
	exit;
	
});


?>



