<?php

namespace Hcode;

use Rain\Tpl;
use PHPMailer;

class Mailer {
		
		/* 
		Para ejecutar colocar los valores correctos
		*/
		const USERNAME = "email-de-envio@gmail.com";
		const PASSWORD = "password_del_email";
		const NAME_FROM = "Hcode Store";
		
		private $mail;
		/*
		toAddress = Email destino
		toName = Nombre asociado al email
		subject = Asunto (titulo del mail)
		tplName = archivo de template que se va a usar
		data =  que se van a pasar para el template
		*/
		public function __construct($toAddress, $toName, $subject, $tplName, $data = array ())
				{
					
				// config template para el cuerpo del mail 
				$config = array(
								"tpl_dir"       	=> $_SERVER["DOCUMENT_ROOT"]."/views/email/",
								"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
								"debug"         	=> false // set to false to improve the speed
				 );

				Tpl::configure( $config );
				
				//  se crea una instancia del template en tpl
				$tpl = new Tpl;
		
		
				// pasamos los datos del template
				foreach ($data as $key => $value) {
					$tpl->assign($key, $value);
				}

				// Asignamos a la variable el contenido del template
				$html = $tpl->draw($tplName, true);
				
		
				//Create a new PHPMailer instance
				// colocamos \ para indicar que esta en el directorio ppal
				$this->mail = new \PHPMailer();

				//Tell PHPMailer to use SMTP
				$this->mail->isSMTP();

				//Enable SMTP debugging
				//SMTP::DEBUG_OFF = off (for production use)
				//SMTP::DEBUG_CLIENT = client messages
				//SMTP::DEBUG_SERVER = client and server messages
				//$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
				$this->mail->SMTPDebug = 0;

				//Set the hostname of the mail server
				$this->mail->Host = 'smtp.gmail.com';
				//Use `$this->mail->Host = gethostbyname('smtp.gmail.com');`
				//if your network does not support SMTP over IPv6,
				//though this may cause issues with TLS

				//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
				$this->mail->Port = 587;

				//Set the encryption mechanism to use - STARTTLS or SMTPS
				// $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$this->mail->SMTPSecure = "tls";

				//Whether to use SMTP authentication
				$this->mail->SMTPAuth = true;

				//Username to use for SMTP authentication - use full email address for gmail
				$this->mail->Username = Mailer::USERNAME;

				//Password to use for SMTP authentication
				$this->mail->Password = Mailer::PASSWORD;

				//Set who the message is to be sent from
				// constante definida con el nombre y email del remitente
				$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

				//Set an alternative reply-to address
				// $this->mail->addReplyTo('replyto@example.com', 'First Last');

				//Set who the message is to be sent to
				// parametros recibidos con  el mail y nombre del destinatario
				$this->mail->addAddress($toAddress, $toName);

				//Set the subject line
				// Parametro recibido con el asunto
				$this->mail->Subject = $subject;

				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				// en este colocamos el texto html que obtenemos a partir del template correspondiente
				$this->mail->msgHTML($html);

				//Replace the plain text body with one created manually
				$this->mail->AltBody = 'This is a plain-text message body';

				//Attach an image file
				// $this->mail->addAttachment('images/phpmailer_mini.png');


				}
				
				
				
				
				public function send() {
					
						Return $this->mail->send();
				}
				
}





?>