<?php
/**
 * Controlador AJAX HTML: listado económico de actividades por casa.
 * Delega en `/src/casas/casa_ingresos_lista_data` para obtener los
 * datos y los pinta con `frontend\shared\web\Lista`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = casas_post_data(PostRequest::getDataFromUrl('/src/casas/casa_ingresos_lista_data', $campos));
$lista = casas_ingresos_lista_from_payload($data);

if (!$lista['ok']) {
    echo $lista['error'] !== '' ? $lista['error'] : (string)_("No se pueden obtener los datos.");
    return;
}

$oLista = new Lista();
$oLista->setGrupos($lista['grupos']);
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
echo $oLista->listaPaginada();
echo htmlspecialchars($lista['nota']);
$errores = $lista['errores'];
if ($errores !== '') {
    echo "<br>";
    echo _("CUIDADO. Falta introducir datos");
    echo "<br>";
    echo $errores;
}
