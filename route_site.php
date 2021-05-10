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

	$page_active = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page_active);
	
	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++){
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}

	
	// Determina los links de las pags anterior y posterior
	$tot_pag=count($pagination);
	if ($page_active > 1) {
		$page_mov['prev'] = '/categories/'.$category->getidcategory().'?page='.($page_active-1);
	} else {
		$page_mov['prev'] = '/categories/'.$category->getidcategory().'?page='.$page_active;
	}
	if ($page_active < $tot_pag) {
		$page_mov['forw'] = '/categories/'.$category->getidcategory().'?page='.($page_active+1);
	} else {
		$page_mov['forw'] = '/categories/'.$category->getidcategory().'?page='.$page_active;
	}



	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages,
		'page_mov'=>$page_mov
	]);
	//exit;
});



?>
