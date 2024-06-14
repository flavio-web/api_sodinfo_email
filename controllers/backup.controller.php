<?php
    require('./helpers/function_date.php');

    class Backup {

        function saveResponseTxt( $response, $pathSave ){

            $data = [
                "status" => true,
                "message" => "",
                "path" => ""
            ];
    
            if ( !file_exists($pathSave) ) {
                mkdir($pathSave, 0777, true);
            }
    
            $datetime = get_DateTime_Ecuador();
            $datetime_format = remplaceFecha( $datetime );
            $fileName = "backup_response_".$datetime_format;
    
            $fileUpload = $pathSave."/".$fileName.".json";
    
            if( $archivo = fopen($fileUpload, "w") ){
             
                if( !fwrite($archivo, json_encode($response) ) ){
                    $data['status'] = false;
                    $data['message'] = 'No se pudo escribir el contenido del archivo';
                    $data['path'] = $fileUpload;
                }
        
                fclose($archivo);
            }else{
                $data['status'] = false;
                $data['message'] = 'El Archivo no se pudo crear';
            }
    
            return $data;
        }
    }

?>