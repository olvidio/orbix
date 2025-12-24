<?php

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;

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

$ConfigSchemaRepository = $GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class);
$oConfigSchema = new ConfigSchema();
$oConfigSchema->setParametroVo(ConfigParametroCode::fromString($Qparametro));
$oConfigSchema->setValorVo(ConfigValor::fromString($Qvalor));
$ConfigSchemaRepository->Guardar($oConfigSchema);
