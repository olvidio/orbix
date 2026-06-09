<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$data = PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_get2_data', [
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
]);
$oLista = new Lista();
$oLista->setCabeceras($data['a_cabeceras']);
$oLista->setDatos($data['a_valores']);
echo $oLista->lista();
$error_txt = $data['overlap_error'];
if ($error_txt) {
    echo "<span class='alert'>$error_txt</span>";
}
if (!empty($data['show_nuevo'])) {
    echo "<br><input type='button' value='" . _('nuevo') . "' onclick=\"fnjs_modificar();\" >";
}
