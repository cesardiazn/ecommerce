<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {
	
	const SESSION = "User";
	// Se altero las constantes de encriptacion, para usar OpenSSL
	const SECRET =  "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_SecreT";


	public static function getFromSession(){

		$user = new User();

		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] >0) {

			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;
	}
	

	// verifica si un usuario esta logado. $inadmin indica si es un usuario administrador
	public static function checkLogin($inadmin = true) {

				if (
					// si la variable de session con el nombre del usuario no existe 
					!isset($_SESSION[User::SESSION])
					||
					// o no tiene valor  
					!$_SESSION[User::SESSION]
					||
					// o el id(user) no es mayor que 0
					!(Int)$_SESSION[User::SESSION]["iduser"] >0
				) {
					// no esta logado
					return false;
				} else { 
					// el usuario esta logado
					// verifico si es un administrador que esta accesando un rota administracion
					// la session existe y es un usuario administrador
					if ($inadmin === true && (bool)$_SESSION[USER::SESSION]['inadmin']===true){
						return true;
					} else if ($inadmin === false) {
						// es un usuario administracion que esta accesando como usuario, esta usando el carrito
						return true;
					} else {
						// no esta logado
						return false;
					}

				}
		
	}



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
		
		
		if (User::checkLogin($inadmin)) {
				
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
	
	
	public static function getForgot($email){
			// Vamos a hacer una consulta a la DB para verificar si tenemos un usuario con el email indicado
			$sql = new Sql();
			
			$results = $sql->select("
				SELECT *
				FROM tb_persons a
				INNER JOIN tb_users b USING(idperson)
				WHERE a.desemail = :email;",
				array(
					":email"=>$email
				));
				
				if (count($results) === 0 ){
					throw new \Exception("Não foi possível recuperar a senha.");
				} else {
					
					$data = $results[0];
					$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array (
						":iduser"=>$data["iduser"],
						":desip"=>$_SERVER["REMOTE_ADDR"]
					));
					
					if (count($results2) === 0 ) {
						throw new \Exception("Não foi possível recuperar a senha.");
					} else {
						$dataRecovery = $results2[0];
						
						/*
						**************** mcrypt_encrypt **********
						ESTA OBSOLETO
						vamos a generar un codigo encriptado, y codificado para envio html
						parametros 
						key = constante secret (clave de encriptacion)
						data = 
						mode = Modo de encriptacion, en este caso se selecciono MCRYPT_MODE_ECB
						
						$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
						
						*/
						//echo "<script>console.log('codidrecovery 1 :".$dataRecovery["idrecovery"]."');</script>";
						$code = openssl_encrypt(
							// json_encode($dataRecovery["idrecovery"]),
							$dataRecovery["idrecovery"],
							'AES-128-CBC',
							User::SECRET,
							0,
							User::SECRET_IV
							);
						// echo "<script>console.log('code 1 :".$code."');</script>";
						// el link para recuperación del password queda 
						$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
						// se envia por email el link 
						
						$mailer = new Mailer($data["desemail"],$data["desperson"],"Redefinir Senha da Hcode Store","forgot", array(
							"name"=>$data["desperson"],
							"link"=>$link
						));
						$mailer->send();
						
						return $data;
					}
				}
	}


	public static function validForgotDecrypt($code){

		// echo "<script>console.log('code :".$code."');</script>";
		// $string = openssl_decrypt($openssl, 'AES-128-CBC', SECRET, 0, SECRET_IV);
		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', User::SECRET, 0, User::SECRET_IV);

		//var_dump($idrecovery);
		//echo "<script>console.log('idrecovery :".$idrecovery."');</script>";
		// echo "<br><br>";

		$sql = new Sql();

		$results = $sql->select("
		SELECT *
		FROM tb_userspasswordsrecoveries a
		INNER JOIN tb_users b USING(iduser)
		INNER JOIN tb_persons c USING(idperson)
		WHERE
			a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL
			AND
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));
		//var_dump($results);
		//echo "<script>console.log('consulta :'".json_encode($results)."');</script>";

		if (count($results)===0){
			throw new \Exception("Não foi possível recuperar a senha.", 1);
		} else {
			return $results[0];
		}
		
	}


	public static function setForgotUsed($idrecovery){

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));
	}


	public function setPassword($password){

		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", Array(
			":password"=>$password,
			"iduser"=>$this->getiduser()
		));
	}

}