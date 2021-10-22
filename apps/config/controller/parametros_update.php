<?php

use config\model\entity\ConfigSchema;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qparametro = (string)  \filter_input(INPUT_POST, 'parametro');
$Qvalor = (string)  \filter_input(INPUT_POST, 'valor');

if ($Qparametro == 'curso_stgr' || $Qparametro == 'curso_crt') {
    $Qini_dia = (integer)  \filter_input(INPUT_POST, 'ini_dia');
    $Qini_mes = (integer)  \filter_input(INPUT_POST, 'ini_mes');
    $Qfin_dia = (integer)  \filter_input(INPUT_POST, 'fin_dia');
    $Qfin_mes = (integer)  \filter_input(INPUT_POST, 'fin_mes');
    $aCursoStgr = [
        'ini_dia' => $Qini_dia,
        'ini_mes' => $Qini_mes,
        'fin_dia' => $Qfin_dia,
        'fin_mes' => $Qfin_mes,
    ];
    $Qvalor = json_encode($aCursoStgr); 
}
    
$oConfigSchema = new ConfigSchema($Qparametro);
$oConfigSchema->setValor($Qvalor);
$oConfigSchema->DBGuardar();
