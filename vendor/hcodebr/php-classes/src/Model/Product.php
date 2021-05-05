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


}