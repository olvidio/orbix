<?php
/**
 * Controlador AJAX HTML: cuerpo del informe `calendario_ubi_resumen`.
 *
 * Obtiene los datos del estudio económico via
 * `/src/casas/calendario_ubi_resumen_data` y los pinta como HTML para
 * que se inyecte en `#exportar` por el JS de
 * `calendario_ubi_resumen.phtml`.
 *
 * Sucesor de `apps/casas/controller/calendario_ubi_resumen_ajax.php`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'id_ubi' => (string)filter_input(INPUT_POST, 'id_ubi'),
    'seccion' => (string)filter_input(INPUT_POST, 'seccion'),
    'G' => (string)filter_input(INPUT_POST, 'G'),
    'inc_t' => (string)filter_input(INPUT_POST, 'inc_t'),
];

$data = casas_post_data(PostRequest::getDataFromUrl('/src/casas/calendario_ubi_resumen_data', $campos));
$errorInfo = casas_calendario_body_error_from_payload($data);

if (!$errorInfo['ok'] && $errorInfo['error'] === 'sin_gastos_anterior') {
    $aQuery = [
        'tipo_lista' => 'datosEcGastos',
        'id_ubi' => $errorInfo['id_ubi'],
        'periodo' => 'ninguno',
        'year' => $errorInfo['any_anterior'],
    ];
    array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
    $web = AppUrlConfig::getPublicAppBaseUrl();
    $pagina = HashFront::link($web . '/frontend/casas/controller/casa.php?' . http_build_query($aQuery));
    $link = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$pagina');\">{$errorInfo['any_anterior']}</span>";
    echo sprintf(_("Falta introducir la información económica (total) del año anterior: %s"), $link);
    echo "<br><br>";
    return;
}

if (!$errorInfo['ok']) {
    echo $errorInfo['error'] !== '' ? $errorInfo['error'] : _("No se pueden calcular los datos solicitados.");
    return;
}

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('calendario_ubi_resumen_body.phtml', $data);
