<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\GuardarEncargoZona;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'guardar_encargo_zona'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_enc' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc'),
    'id_tipo_enc' => \src\shared\domain\helpers\FilterPostGet::post('id_tipo_enc'),
    'id_ubi' => \src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'id_zona' => \src\shared\domain\helpers\FilterPostGet::post('id_zona'),
    'orden' => \src\shared\domain\helpers\FilterPostGet::post('orden'),
    'prioridad' => \src\shared\domain\helpers\FilterPostGet::post('prioridad'),
    'descripcion_lugar' => \src\shared\domain\helpers\FilterPostGet::post('descripcion_lugar'),
    'encargo' => \src\shared\domain\helpers\FilterPostGet::post('encargo'),
    'idioma_enc' => \src\shared\domain\helpers\FilterPostGet::post('idioma_enc'),
    'observ' => \src\shared\domain\helpers\FilterPostGet::post('observ'),
];

/** @var GuardarEncargoZona $useCase */
$useCase = DependencyResolver::get(GuardarEncargoZona::class);
$result = $useCase->execute($input);

ContestarJson::enviar($result['error'], $result['data']);
