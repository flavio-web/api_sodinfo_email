<?php

    require_once("./vendor/autoload.php");
    require('helpers/function_email.php');
    require('request/validated.php');
    require('helpers/function_document.php');
    require('helpers/api_sodinfo_send_email.php');
    $path_raiz = dirname(__DIR__, 1);
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();

    $response = [
        'status' => true,
        'message' => ''
    ];


    try {

        //var_dump($GLOBALS);
        /*$request = json_decode(file_get_contents("php://input"), true);
        print_r($request);*/

        $validated = validaciones( $_POST );
        if( !$validated['status'] ){
            throw new Exception($validated['message']);
        }
    

        $attached = [];
        $server_attached = [];

        if( isset($_POST['autorizacion']) ){
            $datosAuth = getDatosAutorizacion( $_POST['autorizacion'] );
            $mes = $datosAuth['mes'];
            $anio = $datosAuth['anio'];
            $documento = $datosAuth['documento'];
            $ruc = $datosAuth['ruc'];

            $name_file_http = getFileNameRequestByCodeDoc( $documento );

            if( empty($name_file_http) ){
                throw new Exception("El código #".$documento." del documento es incorrecto.");
            }

            //http api_sodinfo_ride
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );

            $pdf_response = json_decode(file_get_contents($_ENV['URL_API_RIDE'].'/'.$name_file_http.'?codigo='.$_POST['autorizacion'], false, stream_context_create($arrContextOptions)), true );
            if( !$pdf_response['status'] ){
                throw new Exception($pdf_response['result']['error_msg']);
            }

            $pathSaveFiles = '';
            if( !isset($_ENV['PATH_FILES']) || !empty($_ENV['PATH_FILES']) ){
                $pathSaveFiles = 'facturas/';
            }else{
                $pathSaveFiles = $_ENV['PATH_FILES'];
            }

            //copiar xml a carpeta especifica
            crearEstructuraCarpetas( __DIR__."/".$pathSaveFiles, $anio, $mes, $documento, $_POST['autorizacion'] );

            //copiar archivo pdf
            $savePdfFile = copiarDocumento( __DIR__."/".$pathSaveFiles.$anio.$mes.'/'.$documento.'/'.$_POST['autorizacion'].'/'.$pdf_response['result']['pdf']['file_name'], $pdf_response['result']['pdf']['file_name'], $pdf_response['result']['pdf']['pdf']);
            if( !$savePdfFile['status'] ){
                throw new Exception($savePdfFile['message']);
            }

            $response_xml = getFileXmlApi( $pdf_response['result']['xml'] );
            //copiar archivo xml
            $name_file = substr($pdf_response['result']['pdf']['file_name'], 0, -3);
            $name_file_xml = $name_file."xml";

            $saveXmlFile = copiarDocumento( __DIR__."/".$pathSaveFiles.$anio.$mes.'/'.$documento.'/'.$_POST['autorizacion'].'/'.$name_file_xml, $name_file_xml, $response_xml, false);
            if( !$saveXmlFile['status'] ){
                throw new Exception($saveXmlFile['message']);
            }

            //agregar pdf como archivo adjunto
           //agregar pdf como archivo adjunto
           $data_file_pdf['file']      = base64_decode($pdf_response['result']['pdf']['pdf']);
           $data_file_pdf['filename']  = $pdf_response['result']['pdf']['file_name'];
           $data_file_pdf['base']      = "base64";
           $data_file_pdf['tipo']      = "application/pdf";
           array_push($server_attached, $data_file_pdf);

           $data_file_xml['file']      = $response_xml;
           $data_file_xml['filename']  = $name_file_xml;
           $data_file_xml['base']      = "base64";
           $data_file_xml['tipo']      = "application/xml";
           array_push($server_attached, $data_file_xml);


            $attached[] = addFiletoAttachment(  __DIR__."/".$pathSaveFiles.$anio.$mes.'/'.$documento.'/'.$_POST['autorizacion'].'/'.$pdf_response['result']['pdf']['file_name'], $pdf_response['result']['pdf']['file_name'] );

            $attached[] =addFiletoAttachment(  __DIR__."/".$pathSaveFiles.$anio.$mes.'/'.$documento.'/'.$_POST['autorizacion'].'/'.$name_file_xml, $name_file_xml );
        }

    
        if( isset($_FILES['attached']) && count($_FILES['attached']) > 0 ){
            for ( $i = 0; $i < count($_FILES['attached']['name']); $i++ ) { 
                $ext = pathinfo($_FILES['attached']['name'][$i], PATHINFO_EXTENSION);
                $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['attached']['name'][$i])) . '.' . $ext;

                if ( !move_uploaded_file($_FILES['attached']['tmp_name'][$i], $uploadfile) ) {
                    throw new Exception("No se pudo leer el archivo ".$_FILES['attached']['name'][$i]);
                }
                
                $data_file['file']      = $uploadfile;
                $data_file['filename']  = $_FILES['attached']['name'][$i];
                $data_file['base']      = "base64";
                $data_file['tipo']      = "application/pdf";
                array_push($attached, $data_file);
            }
        }


        $attachedString = [];
        if( isset($_POST['attachedstring']) && !empty($_POST['attachedstring']) ){
            $attachedString = $_POST['attachedstring'];
        }

        $addReplyTo = [];
        if( isset($_POST['addReplyTo']) && count($_POST['addReplyTo']) > 0 ){
            $addReplyTo = $_POST['addReplyTo'];
        }

        $addCC = [];
        if( isset($_POST['addCC']) && count($_POST['addCC']) > 0 ){
            $addCC = $_POST['addCC'];
        }

        $addBCC = [];
        if( isset($_POST['addBCC']) && count($_POST['addBCC']) > 0 ){
            $addBCC = $_POST['addBCC'];
        }

        $response = sendEmailDefault($_POST['username'], $_POST['password'], $_POST['company'], $_POST['mailTo'], $_POST['subject'], $_POST['message'], $attached, $attachedString, $addReplyTo, $addCC, $addBCC );
                    
        if( !$response['status'] ){
            $response = sendEmailWithApiSodinfo( $_POST['username'], $_POST['password'], $_POST['company'], $_POST['mailTo'], $_POST['subject'], $_POST['message'], [], $server_attached, $addReplyTo, $addCC, $addBCC );
        }

    } catch (Exception $e) {
        $response['status'] = false;
        $response['message'] = $e->getMessage();
    }

    echo json_encode( $response );
?>