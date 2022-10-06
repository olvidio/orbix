<?php

use actividadtarifas\model\entity\GestorTipoTarifa;
use actividadtarifas\model\entity\TipoActivTarifa;
use core\ConfigGlobal;
use web\Desplegable;

/**
 * Esta página muestra un formulario para asociar la tarifa a un tipo de actividad.
 * Si es nueva se puede escojer el tipo de actividad.
 * Si ya existe, sólo se puede modificar la tarifa.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        24/2/09.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oTipoActivTarifa = new TipoActivTarifa();
$aTipoSerie = $oTipoActivTarifa->getArraySerie();

$oDesplPosiblesSeries = new Desplegable();
$oDesplPosiblesSeries->setNombre('serie');
$oDesplPosiblesSeries->setOpciones($aTipoSerie);
$oDesplPosiblesSeries->setOpcion_sel(1);

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$miSfsv = 0;
// -------------- MODIFICAR TARIFA --------------------
if ($Qid_item != 'nuevo') {
    $txt_eliminar = _("¿Está seguro que desea quitar esta tarifa?");

    $oTipoActivTarifa = new TipoActivTarifa(array('id_item' => $Qid_item));
    $id_tarifa = $oTipoActivTarifa->getId_tarifa();
    $serie = $oTipoActivTarifa->getSerie();
    $aTipoSerie = $oTipoActivTarifa->getArraySerie();

    $id_tipo_activ = $oTipoActivTarifa->getId_tipo_activ();
    $oTipoActiv = new web\TiposActividades($id_tipo_activ);
    $isfsv = $oTipoActiv->getSfsvId();

    $oGesTipoTarifa = new GestorTipoTarifa();
    $oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
    $oDesplPosiblesTipoTarifas->setNombre('id_tarifa');
    $oDesplPosiblesTipoTarifas->setOpcion_sel($id_tarifa);

    $oHash = new web\Hash();
    $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
    $oHash->setCamposForm('que!id_tarifa');
    $a_camposHidden = array(
        'id_tipo_activ' => $id_tipo_activ,
        'id_item' => $Qid_item,
        'serie' => $serie,
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash1 = new web\Hash();
    $oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/actividad_tipo_get.php');
    $oHash1->setCamposForm('modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();

    $url_ajax = ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_tipo_actividad_ajax.php';

    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oTipoActiv' => $oTipoActiv,
        'extendida' => FALSE,
        'txt_serie' => $aTipoSerie[$serie],
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'txt_eliminar' => $txt_eliminar,
        'url_ajax' => $url_ajax,
    ];

    $oView = new core\ViewTwig('actividadtarifas/controller');
    echo $oView->render('tarifa_tipo_actividad_form.html.twig', $a_campos);

} else {
    // -------------- NUEVA TARIFA --------------------
    //para una actividad nueva, sólo mi sección.
    $miSfsv = ConfigGlobal::mi_sfsv();

    $txt_eliminar = _("¿Está seguro que desea quitar esta tarifa?");

    $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
    //$Qisfsv = (integer) filter_input(INPUT_POST, 'isfsv');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

    $oActividadTipo = new actividades\model\ActividadTipo();
    $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
    $oActividadTipo->setAsistentes($Qsasistentes);
    $oActividadTipo->setActividad($Qsactividad);
    $oActividadTipo->setNom_tipo($Qsnom_tipo);
    $oActividadTipo->setPara('tipoactiv-tarifas');

    $oGesTipoTarifa = new GestorTipoTarifa();
    $oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($miSfsv);
    $oDesplPosiblesTipoTarifas->setNombre('id_tarifa');

    $oHash = new web\Hash();
    $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
    $oHash->setcamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!serie!id_tarifa');
    $oHash->setCamposNo('id_tipo_activ!que');
    $a_camposHidden = array(
        'id_tipo_activ' => '',
        'que' => '',
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash1 = new web\Hash();
    $oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/actividad_tipo_get.php');
    $oHash1->setCamposForm('modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();


    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oDesplPosiblesSeries' => $oDesplPosiblesSeries,
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'oActividadTipo' => $oActividadTipo,
    ];

    $oView = new core\ViewTwig('actividadtarifas/controller');
    echo $oView->render('tarifa_tipo_actividad_form_nuevo.html.twig', $a_campos);
}
