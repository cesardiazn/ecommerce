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
}