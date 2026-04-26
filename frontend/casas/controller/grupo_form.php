<?php
/**
 * Controlador AJAX HTML: formulario `GrupoCasa` (nuevo/editar).
 *
 * Obtiene los datos de `/src/casas/grupo_form_data` y renderiza
 * `grupo_form.phtml` con los dos desplegables de casas. Sucesor de
 * `apps/casas/controller/grupo_form.php`.
 */

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;

require_once 'frontend/shared/global_header_front.inc';

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$campos = ['id_item' => $Qid_item];
$data = PostRequest::getDataFromUrl('/src/casas/grupo_form_data', $campos);
$payload = is_array($data) ? $data : [];

$es_nuevo = (bool)($payload['es_nuevo'] ?? true);
$id_item = (string)($payload['id_item'] ?? 'nuevo');
$id_ubi_padre = (int)($payload['id_ubi_padre'] ?? 0);
$id_ubi_hijo = (int)($payload['id_ubi_hijo'] ?? 0);
$opciones_casas = $payload['opciones_casas'] ?? [];

$oDesplCasaMadre = new Desplegable('id_ubi_padre', $opciones_casas, $id_ubi_padre, '');
$oDesplCasaHija = new Desplegable('id_ubi_hijo', $opciones_casas, $id_ubi_hijo, '');

$a_campos = [
    'oPosicion' => $oPosicion,
    'es_nuevo' => $es_nuevo,
    'id_item' => $id_item,
    'oDesplCasaMadre' => $oDesplCasaMadre,
    'oDesplCasaHija' => $oDesplCasaHija,
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('grupo_form.phtml', $a_campos);
