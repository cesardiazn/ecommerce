<?php


namespace Hcode;

class Model{
	
	private $values = [];
	
	
	/*
	Este metodo pretende definir metodos en tiempo de ejecucionn para 
	definir u obter los valores de las propiedades 
	*/
	public function __call($name, $args){
		
		// A partir del nombre ($name) obtenemos si es un set/get y el nombre correspondiente de la propiedad
		$method = substr($name, 0, 3);
		$fieldName = substr($name, 3, strlen($name));
		
		switch ($method) {
			
			case "get":
				// return $this->values[$fieldName];
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
			break;
			
			case "set":
				$this->values[$fieldName] = $args[0];
			break;
		}
	}
	
	
	/*
	Este Metodo va a crear otros metodos en tiempo de ejecucion
	Los atributos van a ser definidos a partir de los campos contenidos en el array ($data)
	*/
	public function setData($data = array()){
		
		/*
		echo "<pre>";
		var_dump($data);
		echo "</pre><br><br>-------<br><br>";
		*/
		
		foreach ($data as $key => $value){
			// echo $key . "<br>";
			
			/*
			Defino el nombre del metodo de forma dinamica
			como se esta creando dinamicamente, el nombre lo colocamos entre {}
			El nombre {"set".$key} va a indicar si es un set / get, y el nombre del atributo (variable)
			siempre que creamos nombres en tiempo de ejecucion debemos colocarlos ebtre {}
			*/
			$this->{"set".$key}($value);
		}
	}
	
	public function getValues(){
			return $this->values;
	}
}

?>