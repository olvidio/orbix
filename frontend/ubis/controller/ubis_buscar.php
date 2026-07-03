<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_buscar_data', []));
$opciones = UbisPayload::editarOpcionesFromPayload($data);
$opciones_pais = NotasFormSupport::desplegableOpciones($data['opciones_pais'] ?? []);

$Qsimple = (int)filter_input(INPUT_POST, 'simple');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

$simple = empty($Qsimple) ? 1 : $Qsimple;
$tipo = empty($Qtipo) ? "tot" : $Qtipo;
$loc = empty($Qloc) ? "tot" : $Qloc;

$nomUbi = UbisPayload::buscarNomUbi($tipo);

switch ($tipo) {
    case "ctrdl" :
        $titulo = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("centros de la delegación"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué centro te interesa?"));
        break;
    case "vu_ex" :
        $titulo = strtoupper(_("centros o casas de otras dl/r"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué centro o casa te interesa?"));
        break;
    case "ctrex" :
        $titulo = strtoupper(_("centros de otras dl/r"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué centro te interesa?"));
        break;
    case "cdcdl" :
        $titulo = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("casas de la delegación"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué casa te interesa?"));
        break;
    case "cdcex" :
        $titulo = strtoupper(_("casas de otras dl/r"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué casa te interesa?"));
        break;
    case "mail" :
        $titulo = ucfirst(_("buscar e-mails de los centros de la dl"));
        $tituloGros = ucfirst(_("escoge un grupo de centros"));
        break;
    case "ctrsf" :
        $titulo = strtoupper(_("centros de la sf"));
        $tituloGros = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("¿qué centro te interesa?"));
        break;
    default:
        $titulo = '';
        $tituloGros = '';
        break;
}

$oHash = new HashFront();

$s_camposForm = 'simple!nombre_ubi!opcion!ciudad';
$oHash->setcamposNo('cmb!simple!tipo_ctr!tipo_casa');

if ($simple === 1) {
    $s_camposForm .= '!region!pais';
}
if ($simple === 2) {
    $s_camposForm .= '!tipo!loc';
    if ($loc === "ex") {
        $s_camposForm .= '!dl!region!pais';
    }
}
$oHash->setCamposForm($s_camposForm);

if ($simple === 1) {
    $pagina = HashFront::link('frontend/ubis/controller/ubis_buscar.php?' . http_build_query(['simple' => '2']));
} else {
    $pagina = HashFront::link('frontend/ubis/controller/ubis_buscar.php?' . http_build_query(['simple' => '1']));
}

$a_campos = [
    'oHash' => $oHash,
    'tipo' => $tipo,
    'simple' => $simple,
    'nomUbi' => $nomUbi,
    'opciones_region' => $opciones['opciones_region'],
    'opciones_pais' => $opciones_pais,
    'loc' => $loc,
    'opciones_tipo_casa' => $opciones['opciones_tipo_casa'],
    'opciones_tipo_ctr' => $opciones['opciones_tipo_ctr'],
    'pagina' => $pagina,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_buscar.phtml', $a_campos);
