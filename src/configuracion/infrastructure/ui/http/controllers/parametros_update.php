<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qparametro = input_string($_POST, 'parametro');
$Qvalor = input_string($_POST, 'valor');

if ($Qparametro === 'curso_stgr' || $Qparametro === 'curso_crt') {
    $Qini_dia = input_int($_POST, 'ini_dia');
    $Qini_mes = input_int($_POST, 'ini_mes');
    $Qfin_dia = input_int($_POST, 'fin_dia');
    $Qfin_mes = input_int($_POST, 'fin_mes');
    $aCursoStgr = [
        'ini_dia' => $Qini_dia,
        'ini_mes' => $Qini_mes,
        'fin_dia' => $Qfin_dia,
        'fin_mes' => $Qfin_mes,
    ];
    $Qvalor = json_encode($aCursoStgr) ?: '';
}

/** @var ConfigSchemaRepositoryInterface $ConfigSchemaRepository */
$ConfigSchemaRepository = DependencyResolver::get(ConfigSchemaRepositoryInterface::class);
$oConfigSchema = new ConfigSchema();
$oConfigSchema->setParametroVo(ConfigParametroCode::fromString($Qparametro));
$oConfigSchema->setValorVo(ConfigValor::fromString($Qvalor));
$error_txt = '';
if ($ConfigSchemaRepository->Guardar($oConfigSchema) === false) {
    $error_txt = $ConfigSchemaRepository->getErrorTxt();
}
ContestarJson::enviar($error_txt, 'ok');
