<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\NuevoStatusPeriodo;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_nuevo_status'),
        'nuevo_status'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_zona = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_zona');
$Qperiodo = (string)\src\shared\domain\helpers\FilterPostGet::post('periodo');
$Qempiezamin = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin');
$Qempiezamax = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax');
$Qestado = (int)\src\shared\domain\helpers\FilterPostGet::post('estado', FILTER_VALIDATE_INT);

/** @var NuevoStatusPeriodo $useCase */
$useCase = DependencyResolver::get(NuevoStatusPeriodo::class);
$result = $useCase->execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar($result['error'], []);
