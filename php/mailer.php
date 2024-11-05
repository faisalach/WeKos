<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once './PHPMailer/src/Exception.php';
require_once './PHPMailer/src/PHPMailer.php';
require_once './PHPMailer/src/SMTP.php';

function sendMail($to,$subject,$body)
{
	$host 		= "mail.dokumenku.cloud";
	$sender 	= "wekos@dokumenku.cloud";
	$password 	= "5Ink3r7-pHr7";
	$port 		= 465;

	$mail = new PHPMailer(true);
	try {
	    //Server settings
	    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
	    $mail->isSMTP();                                            //Send using SMTP
	    $mail->Host       = $host;                     //Set the SMTP server to send through
	    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
	    $mail->Username   = $sender;                     //SMTP username
	    $mail->Password   = $password;                               //SMTP password
	    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
	    $mail->Port       = $port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

	    //Recipients
	    $mail->setFrom($sender, 'WeKos');
	    $mail->addAddress($to);     //Add a recipient

	    //Attachments
	    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
	    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

	    //Content
	    $mail->isHTML(true);                                  //Set email format to HTML
	    $mail->Subject = $subject;
	    $mail->Body    = $body;
	    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	    $mail->send();
	    // return 'Message has been sent';
	    return true;
	} catch (Exception $e) {
		// return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		return false;
	}
}
