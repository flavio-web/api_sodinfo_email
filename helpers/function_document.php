<?php

function getDatosAutorizacion( $autorizacion = '' ){
    $mes        = substr($autorizacion, 2, 2);
    $anio       = substr($autorizacion, 4, 4);
    $documento  = substr($autorizacion, 8, 2);
    $ruc        = substr($autorizacion, 10, 13);

    return [
        'mes' => $mes,
        'anio' => $anio,
        'documento' => $documento,
        'ruc' => $ruc
    ];
}

function getFileNameRequestByCodeDoc( $codeDoc = '' ){
    switch ($codeDoc) {
        case '01':
            $name_file_http = 'leer_xml_f_json.php';
            break;
        case '03':
            $name_file_http = 'leer_xml_lc_json.php';
            break;
        case '04':
            $name_file_http = 'leer_xml_nc_json.php';
            break;
        case '05':
            $name_file_http = 'leer_xml_nd_json.php';
            break;
        case '06':
            $name_file_http = 'leer_xml_gr_json.php';
            break;
        case '07':
            $name_file_http = 'leer_xml_r_json.php';
            break;
        
        default:
            $name_file_http = '';
            break;
    }

    return $name_file_http;
}

function crearEstructuraCarpetas($path_principal = '')
{
    if (!is_dir($path_principal)) {
        if (!mkdir($path_principal, 0777, true)) {
            throw new Exception("No se pudo crear el directorio: $path_principal");
        }
    }
}


function copiarDocumento( $path_file, $name_file, $file,  $convert_base64 = true ){

    $response = [
        "status" => true,
        "message" => ""
    ];

    try{

        $path = fopen( $path_file, 'w' );
        if( $convert_base64 ){
            $file = base64_decode( $file );
        }
       
        fwrite( $path, $file );
        fclose( $path );
    
        //validar que se haya grabado el archivo pdf
        if ( !file_exists($path_file) ) {
            throw new Exception("No pudimos grabar el archivo ".$name_file);
        }

    }catch( Exception $e){
        $response['status'] = false;
        $response['message'] = $e->getMessage();
    }

    return $response;
   
}

function getFileXmlApi( $path_xml ){
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
    return file_get_contents($path_xml, false, stream_context_create($arrContextOptions));
}

function generateLogBackup($message_error, $username, $password, $company, $mailTo, $subject, $message, $attached, $attachedString, $addReplyTo, $addCC, $addBCC){
    $backup = new Backup();
    $dataBackup = [
        "username"  => $username,
        "password"  => $password,
        "company"   => $company,
        "mailto"    => $mailTo,
        "subject"   => $subject,
        "message"   => $message,
        "attached"  => $attached,
        "attachedString" => $attachedString,
        "addReplyTo" => $addReplyTo,
        "addCC"     => $addCC,
        "addBCC"    => $addBCC,
        "error"     => $message_error
    ];
    $pathSave = "./public/backup";
    $backup->saveResponseTxt($dataBackup, $pathSave);
}

?>