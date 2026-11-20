<?php

use frontend\actividades\helpers\TipoActivMetadataLoader;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

/**
 * AJAX (`tipo_activ.php`): tabla de los tipos de actividad con el enlace «modificar».
 * Los datos vienen de `/src/actividades/tipo_activ_lista_data`.
 *
 * La pantalla vuelve a pedir la lista después de cada alta, cambio de nombre o borrado: aquí se vacía la caché de
 * sesión de los tipos ({@see TipoActivMetadataLoader}) para que los desplegables de tipo de actividad los vean.
 */
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
TipoActivMetadataLoader::forget();
$data = PostRequest::getDataFromUrl('/src/actividades/tipo_activ_lista_data', []);
$tipos = is_array($data['tipos'] ?? null) ? $data['tipos'] : [];

$texto_link = _("modificar");
$a_valores = [];
$i = 0;
foreach ($tipos as $tipo) {
    if (!is_array($tipo)) {
        continue;
    }
    $i++;
    $id_tipo_activ = (int) ($tipo['id_tipo_activ'] ?? 0);
    $a_valores[$i][1] = $id_tipo_activ;
    $a_valores[$i][2] = (string) ($tipo['nom'] ?? '');
    // HTML propio de la celda (un enlace): `Lista` escapa las celdas que son texto.
    $a_valores[$i][3] = ['html' => "<span class=link id=mod_$id_tipo_activ onclick=fnjs_modificar('$id_tipo_activ')> $texto_link</span>"];
}

$oLista = new Lista();
$oLista->setCabeceras([_("id_tipo_activ"), _("tipo actividad"), _("modificar")]);
$oLista->setDatos($a_valores);

AjaxJsonSupport::html($oLista->lista());
