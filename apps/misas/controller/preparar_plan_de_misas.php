<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use web\Desplegable;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$aOpciones = array(
    'semana_next' => _("próxima semana de lunes a domingo"),
    'mes_next' => _("próximo mes natural"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);

$oFormP->setBoton("<input type=button name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_nuevo_periodo();\">");

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');

$a_TiposPlantilla= array('s'=>'semanal', 'd'=>'semanal y domingos', 'm'=>'mensual');
$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('TipoPlantilla');

$url_crear_nuevo_periodo = 'apps/misas/controller/crear_nuevo_periodo.php';
$oHashNuevoPeriodo = new Hash();
$oHashNuevoPeriodo->setUrl($url_crear_nuevo_periodo);
$oHashNuevoPeriodo->setCamposForm('id_zona!TipoPlantilla!periodo!empiezamin!empiezamax');
$h_nuevo_periodo = $oHashNuevoPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oFormP' => $oFormP,
    'url_crear_nuevo_periodo' => $url_crear_nuevo_periodo,
    'h_nuevo_periodo' => $h_nuevo_periodo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('preparar_nuevo_periodo.html.twig', $a_campos);
