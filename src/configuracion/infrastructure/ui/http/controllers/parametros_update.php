<?php

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'parametros_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qparametro = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'parametro');
$Qvalor = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'valor');

if ($Qparametro === 'curso_stgr' || $Qparametro === 'curso_crt') {
    $Qini_dia = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'ini_dia');
    $Qini_mes = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'ini_mes');
    $Qfin_dia = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'fin_dia');
    $Qfin_mes = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'fin_mes');
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
