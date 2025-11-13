<?php

use src\configuracion\application\repositories\ConfigSchemaRepository;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qparametro = (string)filter_input(INPUT_POST, 'parametro');
$Qvalor = (string)filter_input(INPUT_POST, 'valor');

if ($Qparametro === 'curso_stgr' || $Qparametro === 'curso_crt') {
    $Qini_dia = (integer)filter_input(INPUT_POST, 'ini_dia');
    $Qini_mes = (integer)filter_input(INPUT_POST, 'ini_mes');
    $Qfin_dia = (integer)filter_input(INPUT_POST, 'fin_dia');
    $Qfin_mes = (integer)filter_input(INPUT_POST, 'fin_mes');
    $aCursoStgr = [
        'ini_dia' => $Qini_dia,
        'ini_mes' => $Qini_mes,
        'fin_dia' => $Qfin_dia,
        'fin_mes' => $Qfin_mes,
    ];
    $Qvalor = json_encode($aCursoStgr);
}

$ConfigSchemaRepository = new ConfigSchemaRepository();
$oConfigSchema = new ConfigSchema();
$oConfigSchema->setParametroVo(ConfigParametroCode::fromString($Qparametro));
$oConfigSchema->setValorVo(ConfigValor::fromString($Qvalor));
$ConfigSchemaRepository->Guardar($oConfigSchema);
