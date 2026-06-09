<?php

/**
 * Esta página muestra un formulario para asociar un nombre a un tipo de actividad.
 * Si es nueva se puede escoger el tipo de actividad.
 * Si ya existe, sólo se puede modificar el nombre
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        24/2/09.
 */

use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$web = AppUrlConfig::getPublicAppBaseUrl();

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

if ($Qid_item !== 'nuevo') {
    $txt_eliminar = _('¿Está seguro que desea quitar esta id_tarifa?');

    $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $oTipoActiv = new src\actividades\domain\entity\TiposActividades($Qid_tipo_activ);
    $isfsv = $oTipoActiv->getSfsvId();

    $oHash = new HashFront();
    $oHash->setUrl($web . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ');
    $a_camposHidden = [
        'id_tipo_activ' => $Qid_tipo_activ,
        'id_item' => $Qid_item,
    ];
    $oHash->setArrayCamposHidden($a_camposHidden);

    $oHash1 = new HashFront();
    $oHash1->setUrl($web . '/src/actividades/actividad_tipo_get');
    $oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();

    $url_ajax = $web . '/src/actividadtarifas/relacion_tarifa_update';

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oTipoActiv' => $oTipoActiv,
        'txt_eliminar' => $txt_eliminar,
        'url_ajax' => $url_ajax,
    ];

    $oView = new ViewNewTwig('frontend\\pasarela\\controller');
    $oView->renderizar('nombre_form.html.twig', $a_campos);
} else {
    $miSfsv = \src\shared\config\ConfigGlobal::mi_sfsv();

    $txt_eliminar = _('¿Está seguro que desea borrar este nombre?');

    $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

    $oActividadTipo = new ActividadTipo();
    $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
    $oActividadTipo->setAsistentes($Qsasistentes);
    $oActividadTipo->setActividad($Qsactividad);
    $oActividadTipo->setNom_tipo($Qsnom_tipo);
    $oActividadTipo->setPara('tipoactiv-tarifas');

    $oHash = new HashFront();
    $oHash->setUrl($web . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!nombre_actividad');
    $oHash->setCamposNo('id_tipo_activ');
    $a_camposHidden = [
        'id_tipo_activ' => '',
    ];
    $oHash->setArrayCamposHidden($a_camposHidden);

    $oHash1 = new HashFront();
    $oHash1->setUrl($web . '/src/actividades/actividad_tipo_get');
    $oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oActividadTipo' => $oActividadTipo,
    ];

    $oView = new ViewNewTwig('frontend\\pasarela\\controller');
    $oView->renderizar('nombre_form_nuevo.html.twig', $a_campos);
}
