<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model {
	
	
	public static function listAll() {
		$sql = new Sql();
		
		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}
	

	public function save() {
		
		$sql = new Sql();
		
		
		// Vamos a registrar los datos mediante un procedure creado en la Db, debemos verificar el orden de los parametros (datos)
		$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()
		));
		
		$this->setData($results[0]);

		Category::updateFile();
	}



	public function get($idcategory){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
			':idcategory'=>$idcategory
		]);

		$this->setData($results[0]);

	}


	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
			':idcategory'=>$this->getidcategory()
		]);

		Category::updateFile();
	}


	/*
	Metodo para Actualizar un archivo HTML que va a contener las categorias
	el archivo será actualizado cada vez que se haga alguna actualización de las categorias
	*/
	public static function updateFile() {

		$categories = Category::listAll();

		// definimos un array para guardar los registros (categorias) ya con el formato html 
		$html = [];

		// cargamos el array con la informacion
		foreach ($categories as $row) {
			// href va a contener el link (ruta de la categoria), a partir del id correspondiente
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		/*
		 grabamos el archivo html con el contenido del array,
		 indicamos la ruta a partir de la ruta principal, usando las variables de separador
		 y por ultimo convertimos el array en string.
		 */
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
	}



	public function getProducts($related = true){

		$sql = new Sql();

		if ($related === true) {

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory);", 
					[
						'idcategory'=>$this->getidcategory()
					]
				);
		} else {
			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct NOT IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory);", 
					[
						'idcategory'=>$this->getidcategory()
					]
				);

		}
	}

	/*
	Esta funcion va a traer los productos por paginas 
	recibe los parametros pagina ($page) y la cantidad de productos x pag ($itemsPerPage)
	*/
	public function getProductsPage($page=1, $itemsPerPage=4){

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;
		", [
			':idcategory'=>$this->getidcategory()
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage),
		];

	}

	public function addProduct(Product $product) {

		$sql = new Sql();
		$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)",
			[
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
	}




	public function removeProduct(Product $product) {

		$sql = new Sql();
		$sql->query("DELETE from tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",
			[
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
	}
}




?>