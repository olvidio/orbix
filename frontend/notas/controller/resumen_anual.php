<?php

use src\shared\config\ConfigGlobal;
use core\ViewTwig;
use frontend\shared\model\ViewNewPhtml;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (int)filter_input(INPUT_POST, 'filtro');

$rstgr = ConfigGlobal::mi_ambito() === 'rstgr';

$a_comprobar_n = ['id_tabla' => 'n'];
$a_comprobar_a = ['id_tabla' => 'a'];
$a_n_listado = ['lista' => 1];
$a_n_numeros = ['lista' => 0];
$a_agd_listado = ['lista' => 1];
$a_agd_numeros = ['lista' => 0];
$a_profesores_numeros = ['lista' => 0];
$a_profesores_listado = ['lista' => 1];

if ($rstgr && $Qfiltro === 1) {
    $aChecked = $Qdl;
    $region_stgr = ConfigGlobal::mi_dele();
    $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
    $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new Hash();
    $oHash->setCamposForm('dl');
    $oHash->setcamposNo('dl');
    $oHash->setArrayCamposHidden(['filtro' => 1]);

    if (!empty($Qdl)) {
        $a_comprobar_n = ['id_tabla' => 'n', 'dl' => $Qdl];
        $a_comprobar_a = ['id_tabla' => 'a', 'dl' => $Qdl];
        $a_n_listado = ['lista' => 1, 'dl' => $Qdl];
        $a_n_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_agd_listado = ['lista' => 1, 'dl' => $Qdl];
        $a_agd_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_profesores_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_profesores_listado = ['lista' => 1, 'dl' => $Qdl];
    }
}

$go['comprobar_n'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_n));
$go['comprobar_a'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_a));
$go['n_listado'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_listado));
$go['n_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_numeros));
$go['agd_listado'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_listado));
$go['agd_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_numeros));
$go['profesores_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_numeros));
$go['profesores_listado'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_listado));
$go['asig_faltan'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/asig_faltan_que.php');
$go['filtro'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/notas/controller/resumen_anual.php?' . http_build_query(['filtro' => 1]));

if ($rstgr && $Qfiltro === 1) {
    $url = 'frontend/notas/controller/resumen_anual.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Aplicar filtro"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewTwig('ubis/controller');
    $oView->renderizar('dl_rstgr_que.html.twig', $a_campos);
}

if (!$rstgr || ($rstgr && !($Qfiltro === 1 && empty($Qdl)))) {
    $a_campos = [
        'go' => $go,
        'rstgr' => $rstgr,
    ];

    $oView = new ViewNewPhtml('frontend\\notas\\controller');
    $oView->renderizar('resumen_anual.phtml', $a_campos);
}
