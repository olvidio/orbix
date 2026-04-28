<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once("frontend/shared/global_header_front.inc");

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');

$data = PostRequest::getDataFromUrl('/src/ubis/direcciones_que', ['id_ubi' => $Qid_ubi]);

$oHash = new HashFront();
$oHash->setCamposForm('c_p!ciudad!id_ubi!obj_dir!pais');
$oHash->setArraycamposHidden(['obj_dir' => $Qobj_dir, 'id_ubi' => $Qid_ubi]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'tipo_ubi' => $data['tipo_ubi'],
    'url_tabla' => 'frontend/ubis/controller/direcciones_tabla.php',
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('direcciones_que.phtml', $a_campos);
