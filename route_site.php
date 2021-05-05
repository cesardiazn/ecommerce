<?php

use \Hcode\Page;
use \Hcode\Model\Category;

$app->get('/', function() {

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new Page();
	
	$page->setTpl("index");

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
		'products'=>[] // este array vacio se usa temporalmente para que la pag no de error en el loop
	]);
	//exit;
});


?>
