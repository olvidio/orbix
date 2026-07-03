<?php

use frontend\casas\helpers\CasasPayload;

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
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$campos = ['id_item' => $Qid_item];
$data = CasasPayload::postData(PostRequest::getDataFromUrl('/src/casas/grupo_form_data', $campos));
$form = CasasPayload::grupoFormFromPayload($data);

$oDesplCasaMadre = new Desplegable(
    'id_ubi_padre',
    $form['opciones_casas'],
    CasasPayload::desplegableOpcionSel($form['id_ubi_padre']),
    ''
);
$oDesplCasaHija = new Desplegable(
    'id_ubi_hijo',
    $form['opciones_casas'],
    CasasPayload::desplegableOpcionSel($form['id_ubi_hijo']),
    ''
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'es_nuevo' => $form['es_nuevo'],
    'id_item' => $form['id_item'],
    'oDesplCasaMadre' => $oDesplCasaMadre,
    'oDesplCasaHija' => $oDesplCasaHija,
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('grupo_form.phtml', $a_campos);
