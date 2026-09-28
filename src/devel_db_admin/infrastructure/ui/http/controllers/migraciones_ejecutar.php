<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesEjecutar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;


$capsules = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'ctx_ejecutar');
$ids = [];
foreach ($capsules as $capsule) {
    try {
        $ctx = HashB::open((string) $capsule, 'migraciones_ejecutar');
    } catch (HashBInvalidException $e) {
        continue;
    }
    $id = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id');
    if ($id !== '') {
        $ids[] = $id;
    }
}
if ($capsules === [] || $ids === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var MigracionesEjecutar $useCase */
$useCase = DependencyResolver::get(MigracionesEjecutar::class);

$modoRaw = $_POST['modo'] ?? 'seleccion';
$modo = is_scalar($modoRaw) ? (string) $modoRaw : 'seleccion';
$seleccionados = $ids;
$prefijoHasta = '';
if ($modo === 'hasta') {
    if (count($ids) !== 1) {
        ContestarJson::enviar(_("Operación no autorizada"), 'none');
        return;
    }
    $prefijoHasta = $ids[0];
    $seleccionados = [];
}

$result = $useCase->ejecutar($modo, $seleccionados, $prefijoHasta);

ContestarJson::enviar('', [
    'lines' => $result['lines'],
    'error' => $result['error'],
]);
