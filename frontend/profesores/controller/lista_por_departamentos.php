<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/profesores_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (int)filter_input(INPUT_POST, 'filtro', FILTER_DEFAULT);

$url_backend = '/src/profesores/lista_por_departamentos';
$data = PostRequest::getDataFromUrl($url_backend, [
    'dl' => $Qdl,
    'filtro' => $Qfiltro,
]);

if (($data['modo'] ?? '') === 'filtro') {
    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked(notas_checked_ids_from_post($data['a_checked'] ?? []));
    $oCuadros->setOpciones(notas_desplegable_opciones($data['a_delegaciones'] ?? []));

    $oHash = new HashFront();
    $oHash->setCamposForm('dl');
    $oHash->setcamposNo('dl');
    $oHash->setArrayCamposHidden(['filtro' => 1]);

    $a_campos = [
        'oHash' => $oHash,
        'url' => 'frontend/profesores/controller/lista_por_departamentos.php',
        'boton_txt' => _("Aplicar filtro"),
        'oCuadros' => $oCuadros,
    ];
    $oView = new ViewNewTwig('frontend/ubis/controller');
    $oView->renderizar('dl_rstgr_que.html.twig', $a_campos);
    exit();
}

$a_campos = [
    'aClaustro' => $data['aClaustro'] ?? [],
    'rstgr' => !empty($data['rstgr']),
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('lista_por_departamentos.phtml', $a_campos);
