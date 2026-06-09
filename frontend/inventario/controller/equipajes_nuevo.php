<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';

FrontBootstrap::boot();

$Qperiodo = (int)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (int)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (int)filter_input(INPUT_POST, 'empiezamax');

$aOpciones = [
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'curso_ca' => _('curso ca'),
    'curso_crt' => _('curso crt'),
    'separador1' => '---------',
    'otro' => _('otro'),
];
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel(inventario_periodo_sel_string($Qperiodo));
$oFormP->setDesplAnysOpcion_sel(inventario_periodo_sel_string($Qyear));
$oFormP->setEmpiezaMin(inventario_periodo_sel_string($Qempiezamin));
$oFormP->setEmpiezaMax(inventario_periodo_sel_string($Qempiezamax));
$oFormP->setTitulo(_('periodo de selección de actividades'));

$oHash = new HashFront();
$sCamposForm = 'empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val!id_cdc';
$oHash->setCamposForm($sCamposForm);
$oHash->setCamposNo('id_cdc');

$url_ver_equipajes = HashFront::link('frontend/inventario/controller/equipajes_ver.php');

$a_campos = [
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'url_ver_equipajes' => $url_ver_equipajes,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_nuevo.phtml', $a_campos);
