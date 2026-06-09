<?php

/**
 * Pantalla "actualizar docencia" (menu). Muestra el form de seleccion de
 * periodo; al aceptarlo dispara `DocenciaActualizar`, que graba en
 * `d_docencia_stgr` la docencia calculada a partir de las actividades
 * terminadas del rango.
 *
 * Sucesor de `apps/actividadestudios/controller/actualizar_docencia.php`.
 *
 * La mutación pasa por `/src/actividadestudios/docencia_actualizar` (PostRequest).
 * Sin `use src\...` en el controlador frontend.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qyear = tessera_imprimir_string(filter_input(INPUT_POST, 'year'));
$Qperiodo = tessera_imprimir_string(filter_input(INPUT_POST, 'periodo'));
$Qempiezamin = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamin'));
$Qempiezamax = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamax'));
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
    $oFormP->setTitulo(strtoupper_dlb(_('periodo de selección de actividades')));
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
    $data = actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/docencia_actualizar', [
        'continuar' => $continuar,
        'year' => $Qyear,
        'periodo' => $Qperiodo,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
    ]));
    $a_campos = [
        'mod' => 'fin',
        'txt_rta' => tessera_imprimir_string($data['txt_rta'] ?? ''),
    ];
}

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('actualizar_docencia.phtml', $a_campos);
