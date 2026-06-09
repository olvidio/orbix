<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
header('Content-Type: text/html; charset=UTF-8');

$data = PostRequest::getDataFromUrl('/src/procesos/tipo_activ_proceso_lista', []);
$a_cabeceras = (array)($data['a_cabeceras'] ?? []);
$a_tipos = (array)($data['a_tipos'] ?? []);

$a_valores = [];
$i = 0;
foreach ($a_tipos as $fila) {
    $i++;
    $id_tipo_activ = (string)$fila['id_tipo_activ'];
    $a_valores[$i][1] = $id_tipo_activ;
    $a_valores[$i][2] = (string)$fila['nom'];

    $id_txt_dl = 'dl_' . $id_tipo_activ;
    $a_valores[$i][3] = "<span class=link id=$id_txt_dl onclick=fnjs_cambiar_proceso('$id_tipo_activ','t')> "
        . (string)$fila['nom_proceso_propio'] . "</span>";

    $id_txt_nodl = 'nodl_' . $id_tipo_activ;
    $a_valores[$i][4] = "<span class=link id=$id_txt_nodl onclick=fnjs_cambiar_proceso('$id_tipo_activ','f')> "
        . (string)$fila['nom_proceso_no_propio'] . "</span>";
}

$oLista = new Lista();
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->lista();
