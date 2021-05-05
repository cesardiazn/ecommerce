<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// definimos una ruta para la pagina administrativa
$app->get('/admin', function() {

	// antes de permitir el uso del modulo de administracion del website, verifico el usuario
	User::verifyLogin();

	// al definir la instancia, automaticamente llama al __contructor  y al __destruct
	$page = new PageAdmin();
	
	$page->setTpl("index");

});


?>

