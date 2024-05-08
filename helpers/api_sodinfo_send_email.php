<?php

function sendEmailWithApiSodinfo( $username, $password, $company = '', $mailTo = '', $subject = '', $message = '', $attached = [], $attachedString = [], $addReplyTo = [], $addCC = [], $addBCC = [] ){

    $data = [
        'status' => true,
        'message' => ''
    ];

    try {

        $params = [
            'username' => $username,
            'password' => $password,
            'company' => $company,
            'mailTo' => $mailTo,
            'subject' => $subject,
            'message' => $message,
            'attached' => $attached,
            'attachedString' => $attachedString,
            'addReplyTo' => $addReplyTo,
            'addCC' => $addCC,
            'addBCC' => $addBCC
        ];

        $curl = curl_init(); //inicia la sesión cURL

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sodinfo.com.ec/api_sodinfo_email_server/index.php", //url a la que se conecta
            CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
            CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
            CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
            CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
            CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
            CURLOPT_CUSTOMREQUEST => "POST", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($params),
        )); //curl_setopt_array configura las opciones para una transferencia cURL
        
        //$response = curl_exec($curl);// respuesta generada
        //print_r( $response );
    
        $err = curl_error($curl); // muestra errores en caso de existir
        
        curl_close($curl); // termina la sesión 
        
        if ( $err ) {
            throw new Exception( $err );
        }

        $data['message'] = "Correo electronico enviado correctamente.";

    } catch ( Exception $error ) {
        $data['status'] = false;
        $data['message'] = $error->getMessage();
    }

    return $data;
}

?>