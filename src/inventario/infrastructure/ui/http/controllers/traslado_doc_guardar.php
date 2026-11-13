<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$capsules = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = [];
foreach ($capsules as $capsule) {
    if (!is_string($capsule) || $capsule === '') {
        continue;
    }
    try {
        $ctx = HashB::open((string) $capsule, 'traslado_doc_guardar');
    } catch (HashBInvalidException $e) {
        continue;
    }
    $id_doc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_doc');
    if ($id_doc > 0) {
        $a_sel[] = $id_doc;
    }
}
if ($capsules === [] || $a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
/** @var \src\inventario\application\DocumentosTrasladar $useCase */
$useCase = DependencyResolver::get(\src\inventario\application\DocumentosTrasladar::class);
ContestarJson::enviar($useCase->execute(
    $a_sel,
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi_new'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_lugar_new'),
), 'ok');
