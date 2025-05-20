<?php


// Crea los objetos de uso global **********************************************
use src\shared\ViewSrcPhtml;
use web\Hash;
use web\PeriodoQue;

require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qperiodo = (int)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (int)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (int)filter_input(INPUT_POST, 'empiezamax');

$aOpciones = array(
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'curso_ca' => _('curso ca'),
    'curso_crt' => _('curso crt'),
    'separador1' => '---------',
    'otro' => _('otro')
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setTitulo(_("periodo de selección de actividades"));

$oHash = new Hash();
$sCamposForm = 'empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val!id_cdc';
$oHash->setCamposForm($sCamposForm);
$oHash->setCamposNo('id_cdc');

$url_ver_equipajes = Hash::link('frontend/inventario/controller/equipajes_ver.php');

$a_campos = [
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'url_ver_equipajes' => $url_ver_equipajes,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_nuevo.phtml', $a_campos);
