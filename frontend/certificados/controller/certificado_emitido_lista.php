<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Esta página muestra una tabla con los certificados.
 *
 * @package    delegacion
 * @subpackage    estudios
 */

use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');

$navState = [];
foreach (['titulo', 'certificado'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros($navState, $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);


$mes = date('m');
$oConfig = CertificadosPayload::oConfig();
$fin_m = $oConfig !== null ? $oConfig->getMesFinStgr() : 12;
if ($mes > $fin_m) {
    $any = (int)date('Y') + 1;
} else {
    $any = (int)date('Y');
}
$oInicurs_ca = \frontend\shared\helpers\FuncTablasSupport::cursoEst('inicio', $any);
$oFincurs_ca = \frontend\shared\helpers\FuncTablasSupport::cursoEst('fin', $any);
$inicurs_ca_iso = $oInicurs_ca->getIso();
$fincurs_ca_iso = $oFincurs_ca->getIso();
$inicurs_ca_local = $oInicurs_ca->getFromLocal();
$fincurs_ca_local = $oFincurs_ca->getFromLocal();
$titulo = ucfirst(sprintf(_('lista de certificados emitidos entre %s y %s y no enviados'), $inicurs_ca_local, $fincurs_ca_local));

if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_('Solamente lo pueden ver las regiones del stgr'));
}

$data = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_lista_datos', [
    'certificado' => $Qcertificado,
    'inicurs_ca_iso' => $inicurs_ca_iso,
    'fincurs_ca_iso' => $fincurs_ca_iso,
]));
$tabla = CertificadosPayload::emitidoListaTablaFromPayload($data);
$a_valores = $tabla['valores'];

if (!ListNavSupport::idSelIsEmpty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if ($Qscroll_id !== '') {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('certificado_emitido_lista');
$oTabla->setCabeceras($tabla['cabeceras']);
$oTabla->setBotones($tabla['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('certificado');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh!id_sel');

$pdf_signed_urls = [];
foreach ($a_valores as $idx => $row) {
    if (!is_int($idx) || !is_array($row) || !isset($row['sel'])) {
        continue;
    }
    $id = \frontend\shared\helpers\PayloadCoercion::int($row['sel']);
    if ($id <= 0) {
        continue;
    }
    $pdf_signed_urls[(string) $id] = SignedDownloadToken::urlCertificadoEmitido($id);
}

$txt_eliminar = _('¿Está seguro que quiere eliminar el certificado?');

$a_campos = [
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'pdf_signed_urls_json' => json_encode($pdf_signed_urls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
    'oTabla' => $oTabla,
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewPhtml('frontend\certificados\controller');
$oView->renderizar('certificado_emitido_lista.phtml', $a_campos);
