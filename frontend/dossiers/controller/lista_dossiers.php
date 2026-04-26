<?php
/**
 * Include desde 'home_persona.phtml' y 'home_ubis.phtml' (variables $pau, $id_pau, $Qobj_pau).
 */
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_lista_fichas_data', [
    'pau' => $pau,
    'id_pau' => $id_pau,
    'obj_pau' => $Qobj_pau,
]);
$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('lista_dossiers.phtml', $data);
