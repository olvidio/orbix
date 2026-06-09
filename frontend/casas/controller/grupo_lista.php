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
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = casas_post_data(PostRequest::getDataFromUrl('/src/casas/grupo_lista_data'));
$lista = casas_grupo_lista_from_payload($data);

$oLista = new Lista();
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
echo $oLista->lista();

if ($lista['puede_anadir']) {
    echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nuevo") . '</span>';
}
