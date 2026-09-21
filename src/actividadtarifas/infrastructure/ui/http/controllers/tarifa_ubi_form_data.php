<?php


/**
 * Endpoint backend: datos del form modificar/nuevo `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$ctxForm = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_form');
if ($ctxForm !== '') {
    try {
        $input = HashB::open($ctxForm, 'tarifa_ubi_form');
    } catch (HashBInvalidException $e) {
        ContestarJson::enviar(_("Operación no autorizada"), 'none');
        return;
    }
} else {
    $input = [
        'id_item' => '',
        'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
        'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year'),
        'letra' => '',
    ];
}

/** @var TarifaUbiFormData $useCase */
$useCase = DependencyResolver::get(TarifaUbiFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
