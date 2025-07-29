<?php

use PHPMailer\PHPMailer\PHPMailer;

function sendEmailDefault($username = '', $password = '', $company = '', $mailTo = [], $asunto = "", $mensaje = "", $adjuntos = [], $adjuntosString = [], $replicas = [], $mailCopias = [], $mailCopiasOcultas = []){
    
   
    require_once("./vendor/autoload.php");

    require_once("./vendor/phpmailer/phpmailer/src/Exception.php");
    require_once("./vendor/phpmailer/phpmailer/src/SMTP.php");
    require_once("./vendor/phpmailer/phpmailer/src/Exception.php");

    $response = [
        'status' => false,
        'message' => ''
    ];


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'tls://smtp.gmail.com:587';               //Set the SMTP server to send through
        //$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->SMTPAuth   = "login";
        $mail->Username   = $username;//"sodinfofacturacion@gmail.com";                     //SMTP username
        $mail->Password   = $password;//"txfhqqxzgxfabtqi";      //sodinfo2020                         //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  //Enable implicit TLS encryption
        $mail->SMTPAuth   = "login";
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';                                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //esto es para https
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Recipients
       
        $mail->setFrom($username, $company);
      
        for($i = 0; $i < count($mailTo); $i++){
            $mail->addAddress($mailTo[$i]);    
        }
       
        for($i = 0; $i < count($replicas); $i++){
            $mail->addReplyTo($replicas[$i]);   
        }
        
        for($i = 0; $i < count($mailCopias); $i++){
            $mail->addCC($mailCopias[$i]); 
        }

        for($i = 0; $i < count($mailCopiasOcultas); $i++){
            $mail->addBCC($mailCopiasOcultas[$i]);
        }
       
  
        for($i = 0; $i < count($adjuntos); $i++){
            //Attachments
            if( isset($adjuntos[$i]['filename']) && !empty($adjuntos[$i]['filename']) ){
                $mail->addAttachment($adjuntos[$i]['file'], $adjuntos[$i]['filename']);
            }else{
                $mail->addAttachment($adjuntos[$i]['file']);    
            }  
        }

        for($i = 0; $i < count($adjuntosString); $i++){
            //Attachments
            if( (isset($adjuntosString[$i]['filename']) && !empty($adjuntosString[$i]['filename'])) && (isset($adjuntosString[$i]['base']) && !empty($adjuntosString[$i]['base'])) && (isset($adjuntosString[$i]['tipo']) && !empty($adjuntosString[$i]['tipo']))){
                $mail->addStringAttachment($adjuntosString[$i]['file'], $adjuntosString[$i]['filename'],  $adjuntosString[$i]['base'], $adjuntosString[$i]['tipo']);
            }  
        }

        //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if( !$mail->send() ){
            throw new Exception("Error al intentar enviar email.");
        }

        $response['status'] = true;
        $response['message'] = "Email enviado correctamente.";
       
    } catch (Exception $e) {
        $response['status'] = false;
        $response['message'] = "Email no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }

    return $response;
}

function addFiletoAttachment( $path_file, $file_name = '' ){
    return [
        'file'      => $path_file,
        'filename'  => $file_name,
    ];
}

?>