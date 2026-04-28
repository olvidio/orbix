<?php

/**
 * Pantalla "actualizar docencia" (menu). Muestra el form de seleccion de
 * periodo; al aceptarlo dispara `DocenciaActualizar`, que graba en
 * `d_docencia_stgr` la docencia calculada a partir de las actividades
 * terminadas del rango.
 *
 * Sucesor de `apps/actividadestudios/controller/actualizar_docencia.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use src\actividadestudios\application\DocenciaActualizar;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;

require_once("frontend/shared/global_header_front.inc");
require_once 'apps/core/global_object.inc';

$Qyear = (string) filter_input(INPUT_POST, 'year');
$Qperiodo = (string) filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string) filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) filter_input(INPUT_POST, 'empiezamax');
$continuar = (int) filter_input(INPUT_POST, 'continuar');

if (empty($continuar)) {
    $boton = "<input type='button' value='" . _('buscar') . "' onclick='fnjs_buscar()' >";
    $aOpciones = [
        'tot_any' => _('todo el año'),
        'trimestre_1' => _('primer trimestre'),
        'trimestre_2' => _('segundo trimestre'),
        'trimestre_3' => _('tercer trimestre'),
        'trimestre_4' => _('cuarto trimestre'),
        'separador' => '---------',
        'curso_ca' => _('curso ca'),
        'separador1' => '---------',
        'otro' => _('otro'),
    ];
    $oFormP = new PeriodoQue();
    $oFormP->setFormName('que');
    $oFormP->setTitulo(\src\shared\domain\helpers\strtoupper_dlb(_('periodo de selección de actividades')));
    $oFormP->setPosiblesPeriodos($aOpciones);
    $oFormP->setDesplAnysOpcion_sel($Qyear);
    $oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
    $oFormP->setEmpiezaMax($Qempiezamax);
    $oFormP->setEmpiezaMin($Qempiezamin);
    $oFormP->setBoton($boton);
    $oHashPeriodo = new HashFront();
    $oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
    $oHashPeriodo->setCamposNo('!refresh');
    $oHashPeriodo->setArraycamposHidden(['continuar' => 1]);

    $a_campos = [
        'mod' => 'inicio',
        'oFormP' => $oFormP,
        'oHashPeriodo' => $oHashPeriodo,
    ];
} else {
    $txt_rta = DocenciaActualizar::execute($_POST);
    $a_campos = [
        'mod' => 'fin',
        'txt_rta' => $txt_rta,
    ];
}

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('actualizar_docencia.phtml', $a_campos);
