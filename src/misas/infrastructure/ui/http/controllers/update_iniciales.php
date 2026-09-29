<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\UpdateIniciales;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_update'),
        'update_iniciales'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_sacd = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_sacd');
$Qiniciales = (string)\src\shared\domain\helpers\FilterPostGet::post('iniciales');
$Qcolor = (string)\src\shared\domain\helpers\FilterPostGet::post('color');

/** @var UpdateIniciales $useCase */
$useCase = DependencyResolver::get(UpdateIniciales::class);
$result = $useCase->execute($Qid_sacd, $Qiniciales, $Qcolor);

ContestarJson::enviar($result, ['id_sacd' => $Qid_sacd]);
