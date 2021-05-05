<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){

	User::verifyLogin();
	
	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [
		'products'=>$products
	]);
});



$app->get("/admin/products/create", function(){

	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("products-create");
});


$app->post("/admin/products/create", function(){

	User::verifyLogin();
	
	$product = new Product();

	$product->setData($_POST);

	$product->save();

	/*
	file, es el nombre del campo asignado en el formulario html
	el nombre de la foto no es almacenada en la DB, pues se tiene definido
	que el nombre de la foto guarda relacion directa con el id del producto,
	asi, lo que se hace es guardar la foto con el nombre (idproducto) en el directorio correspondiente
	*/
	$product->setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;
});



$app->get("/admin/products/:idproduct/delete", function($idproduct){

	User::verifyLogin();
	
	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header('Location: /admin/products');
	exit;
});


$app->get("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();
	
	$product = new Product();

	$product->get((int)$idproduct);

	$page = new PageAdmin();

	$page->setTpl("products-update", [
		'product'=>$product->getValues()
	]);
	exit;
});



$app->post("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();
	
	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

	/*
	file, es el nombre del campo asignado en el formulario html
	el nombre de la foto no es almacenada en la DB, pues se tiene definido
	que el nombre de la foto guarda relacion directa con el id del producto,
	asi, lo que se hace es guardar la foto con el nombre (idproducto) en el directorio correspondiente
	*/
	$product->setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;

});


?>