<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (int)filter_input(INPUT_POST, 'filtro');

$rstgr = OrbixRuntime::miAmbito() === 'rstgr';

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
    $region_stgr = OrbixRuntime::miDele();
    $deleg = PostRequest::getDataFromUrl('/src/ubis/delegaciones_region_stgr_data', [
        'region_stgr' => $region_stgr,
    ]);
    $a_delegacionesStgr = $deleg['a_delegaciones'] ?? [];

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new HashFront();
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

$go['comprobar_n'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_n));
$go['comprobar_a'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_a));
$go['n_listado'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_listado));
$go['n_numeros'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_numeros));
$go['agd_listado'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_listado));
$go['agd_numeros'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_numeros));
$go['profesores_numeros'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_numeros));
$go['profesores_listado'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_listado));
$go['asig_faltan'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/asig_faltan_que.php');
$go['filtro'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/resumen_anual.php?' . http_build_query(['filtro' => 1]));

if ($rstgr && $Qfiltro === 1) {
    $url = 'frontend/notas/controller/resumen_anual.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Aplicar filtro"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewNewTwig('frontend/ubis/controller');
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
