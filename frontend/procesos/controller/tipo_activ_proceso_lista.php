<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\procesos\helpers\ProcesosPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/procesos/tipo_activ_proceso_lista', []);
$a_cabeceras = ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? null);
$a_tipos = is_array($data['a_tipos'] ?? null) ? $data['a_tipos'] : [];

$a_valores = [];
$i = 0;
foreach ($a_tipos as $fila) {
    $row = ProcesosPayload::tipoActivRow($fila);
    $i++;
    $id_tipo_activ = $row['id_tipo_activ'];
    $a_valores[$i][1] = $id_tipo_activ;
    $a_valores[$i][2] = $row['nom'];

    $id_txt_dl = 'dl_' . $id_tipo_activ;
    $a_valores[$i][3] = "<span class=link id=$id_txt_dl onclick=fnjs_cambiar_proceso('$id_tipo_activ','t')> "
        . $row['nom_proceso_propio'] . '</span>';

    $id_txt_nodl = 'nodl_' . $id_tipo_activ;
    $a_valores[$i][4] = "<span class=link id=$id_txt_nodl onclick=fnjs_cambiar_proceso('$id_tipo_activ','f')> "
        . $row['nom_proceso_no_propio'] . '</span>';
}

$oLista = new Lista();
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
AjaxJsonSupport::html($oLista->lista());
