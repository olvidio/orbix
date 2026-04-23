<?php
/**
 * Controlador AJAX HTML: listado económico de actividades por casa.
 * Delega en `/src/casas/casa_ingresos_lista_data` para obtener los
 * datos y los pinta con `web\Lista`.
 */

use frontend\shared\PostRequest;
use web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = PostRequest::getDataFromUrl('/src/casas/casa_ingresos_lista_data', $campos);
$payload = is_array($data) ? $data : [];

if (($payload['ok'] ?? false) === false) {
    echo $payload['error'] ?? (string)_("No se pueden obtener los datos.");
    return;
}

$oLista = new Lista();
$oLista->setGrupos($payload['a_grupos'] ?? []);
$oLista->setCabeceras($payload['a_cabeceras'] ?? []);
$oLista->setDatos($payload['a_valores'] ?? []);
echo $oLista->listaPaginada();
echo htmlspecialchars((string)($payload['nota'] ?? ''));
$errores = (string)($payload['errores'] ?? '');
if ($errores !== '') {
    echo "<br>";
    echo _("CUIDADO. Falta introducir datos");
    echo "<br>";
    echo $errores;
}
