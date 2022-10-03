<?php

use actividades\model\entity as actividades;
use web\Periodo;

/**
 * Lista los asistentes de una relación de actividades seleccionada
 *
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)\filter_input(INPUT_POST, 'que');

$Qstatus = (integer)\filter_input(INPUT_POST, 'status');
$Qstatus = empty($Qstatus) ? actividades\ActividadAll::STATUS_ACTUAL : $Qstatus;
$Qid_tipo_activ = (string)\filter_input(INPUT_POST, 'id_tipo_activ');
$Qid_ubi = (integer)\filter_input(INPUT_POST, 'id_ubi');
$Qnom_activ = (string)\filter_input(INPUT_POST, 'nom_activ');
$Qperiodo = (string)\filter_input(INPUT_POST, 'periodo');
$Qyear = (integer)\filter_input(INPUT_POST, 'year');
$Qyear = empty($Qyear) ? date('Y') : $Qyear;
$Qdl_org = (string)\filter_input(INPUT_POST, 'dl_org');
$Qempiezamin = (string)\filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)\filter_input(INPUT_POST, 'empiezamax');

// valores por defeccto
if (empty($Qperiodo)) {
    $Qperiodo = 'actual';
}

// Condiciones de búsqueda.
$aWhere = array();
$aOperador = [];

// Status
if ($Qstatus != actividades\ActividadAll::STATUS_ALL) {
    $aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
    $Qsfsv = (string)\filter_input(INPUT_POST, 'sfsv');
    $Qsasistentes = (string)\filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)\filter_input(INPUT_POST, 'sactividad');
    $Qsnom_tipo = (string)\filter_input(INPUT_POST, 'snom_tipo');

    if (empty($Qssfsv)) {
        if ($mi_sfsv == 1) $Qssfsv = 'sv';
        if ($mi_sfsv == 2) $Qssfsv = 'sf';
    }
    $ssfsv = $Qssfsv;
    $sasistentes = empty($Qsasistentes) ? '.' : $Qsasistentes;
    $sactividad = empty($Qsactividad) ? '.' : $Qsactividad;
    $snom_tipo = empty($Qsnom_tipo) ? '...' : $Qsnom_tipo;
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvText($ssfsv);
    $oTipoActiv->setAsistentesText($sasistentes);
    $oTipoActiv->setActividadText($sactividad);
    $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
} else {
    $oTipoActiv = new web\TiposActividades($Qid_tipo_activ);
    $ssfsv = $oTipoActiv->getSfsvText();
    $sasistentes = $oTipoActiv->getAsistentesText();
    $sactividad = $oTipoActiv->getActividadText();
    $nom_tipo = $oTipoActiv->getNom_tipoText();
}
if ($Qid_tipo_activ != '......') {
    $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
    $aOperador['id_tipo_activ'] = '~';
}
// Lugar
if (!empty($Qid_ubi)) {
    $aWhere['id_ubi'] = $Qid_ubi;
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
    $aOperador['f_fin'] = 'BETWEEN';
} else {
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
}
// dl Organizadora.
if (!empty($Qdl_org)) {
    $aWhere['dl_org'] = $Qdl_org;
}
// Por el nombre
if (!empty($Qnom_activ)) {
    $aWhere['nom_activ'] = '%' . $Qnom_activ . '%';
    $aOperador['nom_activ'] = 'ILIKE';
}
// Publicar
if (!empty($Qmodo) && $Qmodo == 'publicar') {
    $aWhere['publicado'] = 'f';
}
$aWhere['_ordre'] = 'f_ini';

//Para ver el tema plazas. Dos tablas:
//Listar primero las que organiza la dl, después el resto
$mi_dele = core\ConfigGlobal::mi_delef();

/////////////// actividades de mi dl ///////////////////
// si se ha puesto en condición de búsqueda
if (empty($Qdl_org) || $Qdl_org == $mi_dele) {
    $aWhere['dl_org'] = $mi_dele;

    $oListaPlazasDl = new \asistentes\model\listaplazas();
    $oListaPlazasDl->setMi_dele($mi_dele);
    $oListaPlazasDl->setWhere($aWhere);
    $oListaPlazasDl->setOperador($aOperador);
    $oListaPlazasDl->setId_tipo_activ($Qid_tipo_activ);
    // sólo sacd
    if ($Qque == 'list_cjto_sacd') {
        $oListaPlazasDl->setSacd(TRUE);
    }

}
/////////////// actividades de otras dl ///////////////////
// si se ha puesto en condición de búsqueda
if (empty($Qdl_org) || $Qdl_org != $mi_dele) {
    if (!empty($Qdl_org)) {
        $aWhere['dl_org'] = $Qdl_org;
        $aOperador['dl_org'] = '=';
    } else {
        $aWhere['dl_org'] = $mi_dele;
        $aOperador['dl_org'] = '!=';
    }

    $oListaPlazasOtras = new \asistentes\model\listaplazas();
    $oListaPlazasOtras->setMi_dele($mi_dele);
    $oListaPlazasOtras->setWhere($aWhere);
    $oListaPlazasOtras->setOperador($aOperador);
    $oListaPlazasOtras->setId_tipo_activ($Qid_tipo_activ);
}


if (!empty($oListaPlazasDl)) {
    echo "<h3>" . ucfirst(_("actividades de la dl")) . "</h3>";
    // Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
    $oListaDl = $oListaPlazasDl->getLista();
    echo $oListaDl->listaPaginada();
}
if (!empty($oListaPlazasOtras)) {
    echo "<h3>" . ucfirst(_("actividades de otras dl")) . "</h3>";
    // Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
    $oListaOtras = $oListaPlazasOtras->getLista();
    echo $oListaOtras->listaPaginada();
}
