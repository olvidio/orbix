<?php

use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\entity\Ubi;
use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;

$Qid_cdc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_cdc');
$Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo');
$Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year');
$Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin');
$Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax');
$Qinicio = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicio');
$Qfin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'fin');

$error_txt = '';
$a_valores = [];
$nombre_ubi = '';
if (empty($Qid_cdc)) {
    $error_txt = _("debe seleccionar un lugar");
} else {
    $miSfsv = ConfigGlobal::mi_sfsv();
// casa de las actividades organizadas por la dl en el periodo.
    $any = empty($Qyear) ? (int)date('Y') + 1 : $Qyear;
    $mes = date("m");
    if (empty($Qperiodo) || $Qperiodo === 'otro') {
        $inicio = empty($Qinicio) ? $Qempiezamin : $Qinicio;
        $fin = empty($Qfin) ? $Qempiezamax : $Qfin;
    } else {
        $oPeriodo = new Periodo();
        $oPeriodo->setAny($any);
        $oPeriodo->setPeriodo($Qperiodo);
        $inicio = $oPeriodo->getF_ini()->getIso();
        $fin = $oPeriodo->getF_fin()->getIso();
    }

    $oUbi = Ubi::NewUbi($Qid_cdc);
    $nombreUbiVo = $oUbi?->getNombreUbiVo();
    $nombre_ubi = ($nombreUbiVo === null || empty($nombreUbiVo->value())) ? 'sin determinar' : $nombreUbiVo->value();

    $criteria = Criteria::create()
        ->where(Filter::eq(ActividadAllField::IdUbi, $Qid_cdc))
        ->andWhere(Filter::eq(ActividadAllField::DlOrg, ConfigGlobal::mi_dele()))
        ->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, "^$miSfsv"))
        ->andWhere(Filter::lte(ActividadAllField::FIni, $fin))
        ->andWhere(Filter::gte(ActividadAllField::FFin, $inicio))
        ->andWhere(Filter::lt(ActividadAllField::Status, 4))
        ->orderBy(ActividadAllField::FIni);
    /** @var ActividadRepositoryInterface $ActividadRepository */
    $ActividadRepository = DependencyResolver::get(ActividadRepositoryInterface::class);
    $cActividades = $ActividadRepository->matching($criteria);

    $a = 0;
    foreach ($cActividades as $oActividad) {
        $a++;
        $id_activ = $oActividad->getId_activ();
        $f_ini = $oActividad->getF_ini()?->getFromLocal();
        $f_fin = $oActividad->getF_fin()?->getFromLocal();
        $nom_activ = $oActividad->getNom_activ();
        $observ = $oActividad->getObservVo()?->value();

        $a_valores[$a]['sel'] = ['id' => $id_activ, 'select' => ''];
        $a_valores[$a][1] = $f_ini;
        $a_valores[$a][2] = $f_fin;
        $a_valores[$a][3] = $nom_activ;
        $a_valores[$a][4] = $observ;
    }
}

$data = [
    'a_valores' => $a_valores,
    'nombre_ubi' => $nombre_ubi,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
