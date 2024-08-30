<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use misas\domain\entity\EncargoDia;
use web\Desplegable;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$aPeriodos = array(
    'proxima_semana' => _("próxima semana"),
    'proximo_mes' => _("próximo mes"),
    'separador' => '---------',
    'otro' => _("otro")
);

//$oDesplPeriodos = new Desplegable();
//$oDesplPeriodos->setOpciones($aPeriodos);
//$oDesplPeriodos->setNombre('periodos');
//$oDesplPeriodos->setAction('fnjs_nuevo_periodo()');
//$oDesplPeriodos->setAction('fnjs_ver_cuadricula_zona()');

$aOpciones = array(
    'proxima_semana' => _("próxima semana de lunes a domingo"),
    'proximo_mes' => _("próximo mes natural"),
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setisDesplAnysVisible(FALSE);

//$oFormP->setBoton("<input type=button name=\"ver\" value=\"" . _("ver") . "\" onclick=\"fnjs_ver_plantilla_zona();\">");
//$oFormP->setBoton("<input type=button name=\"preparar\" value=\"" . _("preparar") . "\" onclick=\"fnjs_nuevo_periodo();\">");

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$a_TiposPlantilla = array(
    EncargoDia::PLANTILLA_SEMANAL_UNO=>'semanal una opción',
    EncargoDia::PLANTILLA_DOMINGOS_UNO=>'semanal y domingos una opción',
    EncargoDia::PLANTILLA_MENSUAL_UNO=>'mensual una opción',
    EncargoDia::PLANTILLA_SEMANAL_TRES=>'semanal tres opciones',
    EncargoDia::PLANTILLA_DOMINGOS_TRES=>'semanal y domingos tres opciones',
    EncargoDia::PLANTILLA_MENSUAL_TRES=>'mensual tres opciones',
);

$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones($a_TiposPlantilla);
$oDesplTipoPlantilla->setNombre('tipoplantilla');
$oDesplTipoPlantilla->setAction('fnjs_ver_cuadricula_zona()');

$url_crear_nuevo_periodo = 'apps/misas/controller/crear_nuevo_periodo.php';
$oHashNuevoPeriodo = new Hash();
$oHashNuevoPeriodo->setUrl($url_crear_nuevo_periodo);
$oHashNuevoPeriodo->setCamposForm('id_zona!tipoplantilla!periodo!empiezamin!empiezamax');
$h_nuevo_periodo = $oHashNuevoPeriodo->linkSinVal();

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
//    'oDesplPeriodos' => $oDesplPeriodos,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oFormP' => $oFormP,
    'url_crear_nuevo_periodo' => $url_crear_nuevo_periodo,
    'h_nuevo_periodo' => $h_nuevo_periodo,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('preparar_plan_de_misas.html.twig', $a_campos);
