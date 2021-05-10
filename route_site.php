<?php

use \Hcode\Page;
use \Hcode\Model\Category;
use \Hcode\Model\Product;


$app->get('/', function(){

	$products = Product::listAll();

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new Page();

	$page->setTpl("index", [
		'products'=>Product::checkList($products)
	]);
});


$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	/*$ccc=$category->getValues();
	var_dump($ccc);
	exit;
	*/
	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>Product::checkList($category->getProducts())
	]);
	//exit;
});



?>
