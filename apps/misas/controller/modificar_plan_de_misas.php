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

$oFormP->setBoton("<input type=button name=\"ver\" value=\"" . _("ver") . "\" onclick=\"fnjs_ver_plantilla_zona();\">");

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_plantilla_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_plantilla_zona()');

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden');
$h_zona_periodo = $oHashZonaPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'oFormP' => $oFormP,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
];
 
$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_plan_de_misas.html.twig', $a_campos);