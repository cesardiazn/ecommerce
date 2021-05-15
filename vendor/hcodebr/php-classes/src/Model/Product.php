<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model {
	
	
	public static function listAll() {
		$sql = new Sql();
		
		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
	}
	

	public static function checkList($list) {
		
		/* Por cada elemento del array ($list) se va a pasar el valor a $row
		se ejecutan las sentencias y se pasa al siguiente elemento y $row toma el valor
		del siguiente elemento hasta recorrer el array.
		al colocar el & en el $row, permite que alteremos el valor contenido en dicho elemento
		y se reflejado en el array ($list)

		$list es una array que contiene la lista de productos y sus correspondientes datos
		pero le falta el url de la imagen asociada al producto

		siendo solo el url de la imagen el dato faltante, y siendo que el nombre es dado a partir del id del producto
		se podria crear un foreach donde se agregara dicho url a partir de los datos conocidos...
		*/
		foreach ($list as &$row){
			// creamos una instancia de producto
			$p = new Product();
			// asignamos los datos del producto en la instancia
			$p->setData($row);
			// obtenemos todos los datos del producto y se lo asignamos a $row que a su vez va a afectar a $list
			$row = $p->getvalues();
		}

		return $list;
	}


	public function save() {
		
		$sql = new Sql();
		
		
		// Vamos a registrar los datos mediante un procedure creado en la Db, debemos verificar el orden de los parametros (datos)
		$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
			":idproduct"=>$this->getidproduct(),
			":desproduct"=>$this->getdesproduct(),
			":vlprice"=>$this->getvlprice(),
			":vlwidth"=>$this->getvlwidth(),
			":vlheight"=>$this->getvlheight(),
			":vllength"=>$this->getvllength(),
			":vlweight"=>$this->getvlweight(),
			":desurl"=>$this->getdesurl()
		));
		
		$this->setData($results[0]);

	}



	public function get($idproduct){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$idproduct
		]);

		$this->setData($results[0]);

	}


	public function delete(){

		
		/* Ao Eliminar o registro do produto, temos de eliminar o ficheiro de imagen (se existe)
		*/
		$product_file = 
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" .  DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR .
			$this->getidproduct() . ".jpg";

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$this->getidproduct()
		]);

		if (file_exists($product_file)) {unlink($product_file);}

	}


	// Metodo para verificar si un producto tiene un archido de foto asociado
	public function checkPhoto(){

		if (file_exists(
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" .  DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR .
			$this->getidproduct() . ".jpg"
			)) {
				$urlfoto = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
			} else {
				$urlfoto = "/res/site/img/product.jpg";
			}

			// Url de la foto
			return $this->setdesphoto($urlfoto);
			
	}

	// falta agregar el dato del campo foto
	public function getValues() {

		// vamos a utilizar un metodo para verificar si el producto tiene un archivo de foto
		$this->checkPhoto();

		$values = parent::getValues();

		return $values;
	}


	/*
	Metodo para guardar la fotografia en el servidor
	*/
	public function setPhoto($file){

		/*
		Verifico si la variable contiene el nombre de un archivo seleccionado.
		si tiene nombre proceso la imagen, en caso contrario omito el proceso
		para evitar que de error el codigo indicando que $image no existe
		Es posible que el producto ya tenga asignada una foto anterior a la actualizacion
		por lo que puede venir el campo en blanco
		*/
		if ($file["name"]<>'') {
			/*
			determinamos la extencion del archivo para convertir la imagen en formato estandar jpg
			*/
			$extension = explode('.', $file['name']);
			$extension = end($extension);

			$filetype = $extension;
			switch ($extension) {

				case "jpg":
					$filetype = "jpg";
				case "jpeg" :
					$image = imagecreatefromjpeg($file["tmp_name"]);
					$filetype = "jpg";
				break;
				
				case "gif" :
					$image = imagecreatefromgif($file["tmp_name"]);
					$filetype = "jpg";
				break;

				case "png" :
					$image = imagecreatefrompng($file["tmp_name"]);
					$filetype = "jpg";
				break;
			}

			/*
			 Como el archivo seleccionado puede ser de un formato desconocido, solo lo proceso
			 en caso de haber sido reconocido en el paso anterior
			*/
			if ($filetype == "jpg") {
				$nombre_destino = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
				"res" . DIRECTORY_SEPARATOR . 
				"site" . DIRECTORY_SEPARATOR . 
				"img" .  DIRECTORY_SEPARATOR . 
				"products" . DIRECTORY_SEPARATOR .
				$this->getidproduct() . ".jpg";

				imagejpeg($image, $nombre_destino);
				
				imagedestroy($image);
			}
		}

		$this->checkPhoto();

	}

	public function getFromURL($desurl)
	{
		$sql = new Sql();

		$rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [
			':desurl'=>$desurl
		]);

		$this->setData($rows[0]);
	}

	public function getCategories()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories a 
			INNER JOIN tb_productscategories b 
			ON a.idcategory = b.idcategory
			WHERE b.idproduct = :idproduct", [ ':idproduct'=>$this->getidproduct()
			]);

	}

}