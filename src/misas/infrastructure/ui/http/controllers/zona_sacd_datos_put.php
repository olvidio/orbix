<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\ZonaSacdDatosPut;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_put'),
        'zona_sacd_datos_put'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_zona = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_zona');
$Qid_sacd = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_sacd');

/** @var ZonaSacdDatosPut $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosPut::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd, [
    'propia' => (string)\src\shared\domain\helpers\FilterPostGet::post('propia'),
    'dw1' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw1'),
    'dw2' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw2'),
    'dw3' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw3'),
    'dw4' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw4'),
    'dw5' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw5'),
    'dw6' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw6'),
    'dw7' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw7'),
]);

ContestarJson::enviar($result['error']);
