<?php

use frontend\actividadestudios\helpers\ActividadestudiosDesplegableSupport;
use frontend\actividadestudios\helpers\CaPosiblesQuePayload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use src\shared\domain\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$restored = ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionIntoReturnParametros(ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));



$Qna = PayloadCoercion::string(filter_input(INPUT_POST, 'na'));
$Qid_ctr_n = PayloadCoercion::string(filter_input(INPUT_POST, 'id_ctr_n'));
$Qid_ctr_agd = PayloadCoercion::string(filter_input(INPUT_POST, 'id_ctr_agd'));
$Qiasistentes_val = PayloadCoercion::string(filter_input(INPUT_POST, 'iasistentes_val'));
$Qiactividad_val = PayloadCoercion::string(filter_input(INPUT_POST, 'actividad_val'));
$Qperiodo = PayloadCoercion::string(filter_input(INPUT_POST, 'periodo'));
$Qyear = PayloadCoercion::string(filter_input(INPUT_POST, 'year'));
$Qempiezamax = PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamax'));
$Qempiezamin = PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamin'));
$Qref = PayloadCoercion::string(filter_input(INPUT_POST, 'ref'));
$Qgrupo_estudios = PayloadCoercion::string(filter_input(INPUT_POST, 'grupo_estudios'));
$Qca_estudios = PayloadCoercion::string(filter_input(INPUT_POST, 'ca_estudios'));
$Qca_repaso = PayloadCoercion::string(filter_input(INPUT_POST, 'ca_repaso'));
$Qca_todos = PayloadCoercion::string(filter_input(INPUT_POST, 'ca_todos'));

$dq = CaPosiblesQuePayload::fromPayload(
    ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/ca_posibles_que_data', []))
);
$grupo_estudios = $dq['grupo_estudios'];
$mi_grupo = $dq['mi_grupo'];
$aCentrosNExt = $dq['aCentrosNExt'];
$aCentrosAgdExt = $dq['aCentrosAgdExt'];

$oDesplCtrN = new Desplegable();
$oDesplCtrN->setNombre('id_ctr_n');
$oDesplCtrN->setOpciones($aCentrosNExt);
$oDesplCtrN->setOpcion_sel($Qid_ctr_n);
$oDesplCtrN->setBlanco(ActividadestudiosDesplegableSupport::blanco(1));
$oDesplCtrN->setAction("fnjs_n_a('n')");

$oDesplCtrAgd = new Desplegable();
$oDesplCtrAgd->setNombre('id_ctr_agd');
$oDesplCtrAgd->setOpciones($aCentrosAgdExt);
$oDesplCtrAgd->setOpcion_sel($Qid_ctr_agd);
$oDesplCtrAgd->setBlanco(ActividadestudiosDesplegableSupport::blanco(1));
$oDesplCtrAgd->setAction("fnjs_n_a('agd')");

$any = empty($Qyear) ? date('Y') : $Qyear;
$aOpciones = array(
    'verano' => _("verano"),
    'curso_ca' => _("curso"),
    'separador' => '---------',
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador1' => '---------',
    'otro' => _("otro")
);
$oFormP = new frontend\shared\web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("periodo de las actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($any);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new HashFront();
$oHash->setCamposForm('id_ctr_agd!id_ctr_n!texto!empiezamax!empiezamin!periodo!ref!iactividad_val!iasistentes_val!year');
$oHash->setCamposNo('na!grupo_estudios!ca_estudios!ca_repaso!ca_todos');
$a_camposHidden = array(
    'asistentes_val' => 1,
    'actividades_val' => 2
);
$oHash->setArraycamposHidden($a_camposHidden);

if ($Qgrupo_estudios === 'todos') {
    $chk_todos = 'checked';
    $chk_grupo = '';
} else {
    $chk_todos = '';
    $chk_grupo = 'checked';
}

if (empty($stack) && empty($Qca_todos)) {
    $Qca_todos = TRUE;
}

$chk_estudios = FuncTablasSupport::isTrue($Qca_estudios) ? 'checked' : '';
$chk_repaso = FuncTablasSupport::isTrue($Qca_repaso) ? 'checked' : '';
$chk_ca_todos = FuncTablasSupport::isTrue($Qca_todos) ? 'checked' : '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'grupo_estudios' => $grupo_estudios,
    'mi_grupo' => $mi_grupo,
    'oFormP' => $oFormP,
    'oDesplCtrAgd' => $oDesplCtrAgd,
    'oDesplCtrN' => $oDesplCtrN,
    'na' => $Qna,
    'ref' => $Qref,
    'chk_todos' => $chk_todos,
    'chk_grupo' => $chk_grupo,
    'chk_estudios' => $chk_estudios,
    'chk_repaso' => $chk_repaso,
    'chk_ca_todos' => $chk_ca_todos,
    'locale_us' => OrbixRuntime::isLocaleUs(),
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('ca_posibles_que.phtml', $a_campos);
