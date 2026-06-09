<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesEjecutar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


/** @var MigracionesEjecutar $useCase */
$useCase = DependencyResolver::get(MigracionesEjecutar::class);

$modoRaw = $_POST['modo'] ?? 'seleccion';
$modo = is_scalar($modoRaw) ? (string) $modoRaw : 'seleccion';
$seleccionadosRaw = $_POST['sel'] ?? [];
if (!is_array($seleccionadosRaw)) {
    $seleccionadosRaw = [$seleccionadosRaw];
}
$seleccionados = [];
foreach ($seleccionadosRaw as $seleccionado) {
    if (is_scalar($seleccionado) && (string) $seleccionado !== '') {
        $seleccionados[] = (string) $seleccionado;
    }
}
$prefijoHastaRaw = $_POST['prefijo_hasta'] ?? '';
$prefijoHasta = is_scalar($prefijoHastaRaw) ? (string) $prefijoHastaRaw : '';

$result = $useCase->ejecutar($modo, $seleccionados, $prefijoHasta);

ContestarJson::enviar('', [
    'lines' => $result['lines'],
    'error' => $result['error'],
]);
