<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\casas\helpers\CasasPayload;

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

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = CasasPayload::postData(PostRequest::getDataFromUrl('/src/casas/grupo_lista_data'));
$lista = CasasPayload::grupoListaFromPayload($data);

$oLista = new Lista();
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
$html = $oLista->lista();

if ($lista['puede_anadir']) {
    $html .= '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("nuevo") . '</span>';
}
AjaxJsonSupport::html($html);
