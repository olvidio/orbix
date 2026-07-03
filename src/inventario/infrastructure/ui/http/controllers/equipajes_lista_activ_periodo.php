<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;

$Qid_cdc = FuncTablasSupport::inputInt($_POST, 'id_cdc');
$Qperiodo = FuncTablasSupport::inputString($_POST, 'periodo');
$Qyear = FuncTablasSupport::inputInt($_POST, 'year');
$Qempiezamin = FuncTablasSupport::inputString($_POST, 'empiezamin');
$Qempiezamax = FuncTablasSupport::inputString($_POST, 'empiezamax');
$Qinicio = FuncTablasSupport::inputString($_POST, 'inicio');
$Qfin = FuncTablasSupport::inputString($_POST, 'fin');

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

    $aWhere['id_ubi'] = $Qid_cdc;
    $aWhere['dl_org'] = ConfigGlobal::mi_dele();
    $aWhere['id_tipo_activ'] = "^$miSfsv";
    $aOperador['id_tipo_activ'] = "~";
    $aWhere['f_ini'] = $fin;
    $aOperador['f_ini'] = '<=';
    $aWhere['f_fin'] = $inicio;
    $aOperador['f_fin'] = '>=';
    $aWhere['status'] = 4;
    $aOperador['status'] = '<';
    $aWhere['_ordre'] = 'f_ini';
    /** @var ActividadRepositoryInterface $ActividadRepository */
$ActividadRepository = DependencyResolver::get(ActividadRepositoryInterface::class);
    $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);

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
