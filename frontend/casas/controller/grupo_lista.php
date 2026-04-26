<?php
/**
 * Controlador AJAX HTML: listado de `GrupoCasa`.
 *
 * Obtiene los datos de `/src/casas/grupo_lista_data` y los pinta con
 * `frontend\shared\web\Lista`. Sucesor de la lista que construía
 * `apps/casas/controller/grupo_lista.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/casas/grupo_lista_data');
$payload = is_array($data) ? $data : [];

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$puede_anadir = (bool)($payload['puede_anadir'] ?? false);

$oLista = new Lista();
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->lista();

if ($puede_anadir) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nuevo") . '</span>';
}
