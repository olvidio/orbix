<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\DesplegableArray;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$aOpciones = array(
    'get_labor' => _("labor"),
    'get_num' => _("pi, cartas, nº buzón"),
    'get_plazas' => _("sede, plazas")
);
$oDesplOpciones = new DesplegableArray('', $aOpciones, '');
$oDesplOpciones->setBlanco('t');
$oDesplOpciones->setNombre('que');

$url_form_labor = 'frontend/ubis/controller/centros_form_labor.php';
$url_form_num = 'frontend/ubis/controller/centros_form_num.php';
$url_form_plazas = 'frontend/ubis/controller/centros_form_plazas.php';
$url_update = AppUrlConfig::getApiBaseUrl() . '/src/ubis/centros_update';
$url_get_labor = 'frontend/ubis/controller/centros_get_labor.php';
$url_get_num = 'frontend/ubis/controller/centros_get_num.php';
$url_get_plazas = 'frontend/ubis/controller/centros_get_plazas.php';

$oHashMod = new HashFront();
$oHashMod->setUrl($url_form_labor);
$oHashMod->setCamposForm('id_ubi');
$h_form_labor = $oHashMod->linkSinValParams();

$oHashMod = new HashFront();
$oHashMod->setUrl($url_form_num);
$oHashMod->setCamposForm('id_ubi');
$h_form_num = $oHashMod->linkSinValParams();

$oHashMod = new HashFront();
$oHashMod->setUrl($url_form_plazas);
$oHashMod->setCamposForm('id_ubi');
$h_form_plazas = $oHashMod->linkSinValParams();

$oHashLabor = new HashFront();
$oHashLabor->setUrl($url_get_labor);
$oHashLabor->setCamposForm('que');
$param_ajax_labor = $oHashLabor->getParamAjax();

$oHashNum = new HashFront();
$oHashNum->setUrl($url_get_num);
$oHashNum->setCamposForm('que');
$param_ajax_num = $oHashNum->getParamAjax();

$oHashPlazas = new HashFront();
$oHashPlazas->setUrl($url_get_plazas);
$oHashPlazas->setCamposForm('que');
$param_ajax_plazas = $oHashPlazas->getParamAjax();

$a_campos = ['oPosicion' => $oPosicion,
    'url_form_labor' => $url_form_labor,
    'url_form_num' => $url_form_num,
    'url_form_plazas' => $url_form_plazas,
    'url_update' => $url_update,
    'url_get_labor' => $url_get_labor,
    'url_get_num' => $url_get_num,
    'url_get_plazas' => $url_get_plazas,
    'h_form_labor' => $h_form_labor,
    'h_form_num' => $h_form_num,
    'h_form_plazas' => $h_form_plazas,
    'param_ajax_labor' => $param_ajax_labor,
    'param_ajax_num' => $param_ajax_num,
    'param_ajax_plazas' => $param_ajax_plazas,
    'oDesplOpciones' => $oDesplOpciones,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('centros_que.phtml', $a_campos);
