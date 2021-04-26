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
	
	
	public static function listAll() {
		$sql = new Sql();
		
		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}
	
	public function save() {
		
		$sql = new Sql();
		
		
		// Vamos a registrar los datos mediante un procedure creado en la Db, debemos verificar el orden de los parametros (datos)
		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
			array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));
		
		$this->setData($results[0]);
	}

	
	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser
		));

		$data = $results[0];

		$data['desperson'] = utf8_encode($data['desperson']);


		$this->setData($data);

	}
	
	

	public function update() {
		
		$sql = new Sql();
		
		
		// Vamos a registrar los datos mediante un procedure creado en la Db, debemos verificar el orden de los parametros (datos)
		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
			array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));
		
		$this->setData($results[0]);
	}

	
	
	public function delete() {
		
		$sql = new Sql();
		
		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
			));
	}
}