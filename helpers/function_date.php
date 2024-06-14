<?php
function get_DateTime_Ecuador(){
    date_default_timezone_set('America/Guayaquil');
    return date('Y-m-d H:i:s');
}

function remplaceFecha( $fecha ){
    return str_replace(["-", " ", ":"], "_", $fecha);
}
?>