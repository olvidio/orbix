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
    'esta_semana' => _("esta semana"),
    'este_mes' => _("este mes"),
    'proxima_semana' => _("próxima semana"),
    'proximo_mes' => _("próximo mes"),
    'separador' => '---------',
    'otro' => _("otro")
);

$oDesplPeriodos = new Desplegable();
$oDesplPeriodos->setOpciones($aPeriodos);
$oDesplPeriodos->setNombre('periodos');
$oDesplPeriodos->setAction('fnjs_ver_cuadricula_zona()');

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'oDesplPeriodos' => $oDesplPeriodos,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
];
 
$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_plan_de_misas.html.twig', $a_campos);