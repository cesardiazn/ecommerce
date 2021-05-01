<?php 
session_start();

// define las dependencias
require_once("vendor/autoload.php");

// name space
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;


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


$app->get("/admin/categories", function(){

	User::verifyLogin();
	
	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories
	]);
});



$app->get("/admin/categories/create", function(){

	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("categories-create");
});


$app->post("/admin/categories/create", function(){

	User::verifyLogin();
	
	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;
});


$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();
	
	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: /admin/categories');
	exit;
});


$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();
	
	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);
	exit;
});



$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();
	
	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;

});



$app->run();



	/*
	$sql = new Hcode\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	
	echo "<pre>". json_encode($results, JSON_PRETTY_PRINT) . "</pre>";
	*/

 ?>