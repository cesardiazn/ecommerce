<?php


namespace Hcode;


class PageAdmin extends Page {

	// $opts son los atributos pasados al llamar a la funcion
	public function __construct($opts = array(), $tpl_dir = "/views/admin/"){
	
		// llamamos a la clase constructor de la clase padre Page y le pasamos las variables
		parent::__construct($opts, $tpl_dir);
	}
	
}



?>