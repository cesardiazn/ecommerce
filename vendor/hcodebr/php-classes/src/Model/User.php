<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {
	
	const SESSION = "User";
	
	public static function login($Login, $password) {
		
		$sql = new Sql();
		
		$results = $sql->select("SELECT * FROM  tb_users WHERE deslogin = :LOGIN", array (
			":LOGIN"=>$Login
		));
		
		if (count($results) === 0) {
			throw new \Exception("Usuario inexistente ou senha inválida.");
		}
		
		$data = $results[0];
		
		if (password_verify($password, $data["despassword"]) === true) {
			
			$user = new User();
			
			
			// $user->setiduser($data["iduser"]);
			// Vamos a llamar al metodo que pasara los parametros necesarios para que la clase Model pueda definir el metodo necesario
			$user->setData($data);
			
			/*
			User::SESSION
			Obtiene el valor de la constante SESSION, definida en la clase User
			
			En este caso estamos definiendo una variable de session que se llamara User (porque es el valor de la constante)
			y se le esta asignando la informacion de las propiedades del usuario a travez del uso del metod getValues()
			*/
			$_SESSION[User::SESSION] = $user->getValues();
			
			
			/*
			echo "<br> Contenido de la variable de Session <br>";
			var_dump($_SESSION[User::SESSION]);
			exit;
			*/
			return $user;
			
		} else {
			throw new \Exception("Usuario inexistente ou senha inválida.");
		}
	}
	
	
	
	/*
	Verificacion del login del usuario
	la variable $inadmin se va a utilizar para saber si el usuario tiene acceso al modulo de administracion de la web
	se define por defecto con el valor true
	*/
	public static function verifyLogin($inadmin = true){
		
		
		if (
			// si la variable de session con el nombre del usuario no existe 
			!isset($_SESSION[User::SESSION])
			||
			// o no tiene valor  
			!$_SESSION[User::SESSION]
			||
			// o el id(user) no es mayor que 0
			!(Int)$_SESSION[User::SESSION]["iduser"] >0
			||
			// o el valor de inadmin de la sesion es diferente el valor de $inadmin
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin 
			) {
				
				// si la verificacion de la sesion del usuario no es valida, reenvio al usuario al login
				header("Location: /admin/login");
				exit;
			}
	}


	public static function logout(){
		
		$_SESSION[User::SESSION] = NULL;
	}
	
}