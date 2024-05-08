<?php

function validaciones( $request ){

    $response = [
        "status" => true,
        "message" => ""
    ];

    try {
        if( !isset($request['username']) || empty($request['username'])){
            throw new Exception("El email emisor es obligatorio.");
        }
    
        if( !isset($request['password']) || empty($request['password'])){
            throw new Exception("La contraseña email emisor es obligatorio.");
        }
    
        if( !isset($request['company']) || empty($request['company'])){
            throw new Exception("El nombre de la empresa es obligatorio.");
        }
    
        if( !isset($request['message']) || empty($request['message'])){
            throw new Exception("El mensaje del correo a enviar es obligatorio.");
        }
    
        if( !isset($request['subject']) || empty($request['subject'])){
            throw new Exception("El asunto del correo a enviar es obligatorio.");
        }
    
        if( !isset($request['mailTo']) || empty($request['mailTo']) || count($request['mailTo']) === 0 ){
            throw new Exception("El listado de emails destinarios es obligatorio.");
        }

        if( isset($_POST['autorizacion']) ){
            if( strlen($_POST['autorizacion']) != 49 ){
                throw new Exception("El número de autorización debe de tener 49 caracteres.");
            }
        }
        
    } catch ( Exception $e) {
        $response['status'] = false;
        $response['message'] = $e->getMessage();
    }

    return $response;

}

?>