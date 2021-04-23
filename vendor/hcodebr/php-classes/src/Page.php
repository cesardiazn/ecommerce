<?php


namespace Hcode;

use Rain\Tpl;

class Page {
	
	// definimos private para que otras clases no tengan acceso
	private $tpl;
	private $options = []; // define los atributos a utilizar
	private $defaults = [
		"data"=>[]
		]; // define los atributos por defecto
	
	/*
	$opts son los atributos pasados al llamar a la funcion
	se define $tpl_dir para poder utilizar el mismo metodo con otra clase, que usa otro directorio
	se asigna por defecto el valor /view/
	*/
	public function __construct($opts = array(), $tpl_dir = "/views/"){
		
	// usamos array_merge para definir el atributo ( options )
	// a partir de $default aplicando los parametros de $opts
	$this -> options = array_merge($this->defaults, $opts);
		
	// config
	$config = array(
					"tpl_dir"       	=> $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
					"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
					"debug"         	=> false // set to false to improve the speed
	 );

	Tpl::configure( $config );
	
	//  se crea una instancia del template en tpl
	$this->tpl = new Tpl;
	
	$this->setData($this->options["data"]);
	
	// usamos el metodo draw para definir la cabecera de pag que se va a mostrar
	$this->tpl->draw("header");
		
	}
	
	
	/*
	Metodo para asignar el array($data)
	*/
	private function setData($data = array()) {

		/*
		$data es un array, definimos $value con el contenido de $data en la clave $key
		*/
		foreach ($data as $key => $value) {
				$this->tpl->assign($key, $value); // usamos el metodo assign 
				
		}	
		
	}
	
	/*
	Metodo para Definir el contenido de la pag.
	$name = nombre del template
	$data = datos 
	$returnHTML  = define el tipo de salida
	*/
	public function setTpl($name, $data = array(), $returnHTML = false){
		
		$this->setData($data);
		
		return $this->tpl->draw($name, $returnHTML);
		
	}
	
	public function __destruct(){
		
		$this->tpl->draw("footer");
	}
}



?>