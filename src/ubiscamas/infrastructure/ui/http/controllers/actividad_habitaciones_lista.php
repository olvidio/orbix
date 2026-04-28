<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\web\ContestarJson;
use src\ubiscamas\application\HabitacionesCamaLista;
use frontend\shared\security\HashFront;

$Qid_activ = (string)filter_input(INPUT_POST, 'id_activ');

$HabitacionCamaLista = new HabitacionesCamaLista();
$data = $HabitacionCamaLista((int)$Qid_activ);

if (!empty($data['success'])) {
    $web = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
    $id_activ = (int)$data['id_activ'];

    $url_actualizar = 'frontend/ubiscamas/controller/lista_habitaciones.php';
    $oHashActualizar = new HashFront();
    $oHashActualizar->setUrl($url_actualizar);
    $oHashActualizar->setCamposNo('refresh');
    $oHashActualizar->setArraycamposHidden([
        'id_activ' => $id_activ,
        'refresh' => 1,
    ]);
    $data['reload_main_url'] = $web . '/' . $oHashActualizar->getUrl() . '?' . $oHashActualizar->linkConVal();

    $url_update_cama = 'src/ubiscamas/update_cama_asistente';
    $oHashUpdateCama = new HashFront();
    $oHashUpdateCama->setUrl($url_update_cama);
    $oHashUpdateCama->setCamposForm('id_activ!id_nom!id_cama');
    $data['url_update_cama_full'] = $web . '/' . $url_update_cama;
    $data['hash_update_cama_ajax'] = $oHashUpdateCama->getParamAjaxEnArray();

    $url_update_solo_vip = '/src/ubiscamas/update_solo_vip';
    $oHashSoloVip = new HashFront();
    $oHashSoloVip->setUrl($url_update_solo_vip);
    $oHashSoloVip->setArraycamposHidden(['id_activ' => $id_activ]);
    $oHashSoloVip->setCamposChk('solo_vip');
    $data['url_update_solo_vip'] = $url_update_solo_vip;
    $data['update_solo_vip_full_url'] = $web . $url_update_solo_vip;
    $data['hash_solo_vip_ajax'] = $oHashSoloVip->getParamAjaxEnArray();

    $url_distribucion = 'frontend/ubiscamas/controller/lista_habitaciones_distribucion.php';
    $oHashDistribucion = new HashFront();
    $oHashDistribucion->setUrl($url_distribucion);
    $oHashDistribucion->setArraycamposHidden(['id_activ' => $id_activ]);
    $data['distribucion_open_url'] = $web . '/' . $url_distribucion . '?' . $oHashDistribucion->linkConVal();

    $url_nombres = 'frontend/ubiscamas/controller/lista_habitaciones_nombres.php';
    $oHashNombres = new HashFront();
    $oHashNombres->setUrl($url_nombres);
    $oHashNombres->setArraycamposHidden(['id_activ' => $id_activ]);
    $data['nombres_open_url'] = $web . '/' . $url_nombres . '?' . $oHashNombres->linkConVal();
}

$error_txt = '';
ContestarJson::enviar($error_txt, $data);
