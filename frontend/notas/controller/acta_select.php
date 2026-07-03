<?php

use frontend\notas\helpers\NotasPayload;
use frontend\shared\helpers\ListNavSupport;

/**
 * Esta página muestra una tabla con las actas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        14/10/03.
 *
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$mi_dele = OrbixRuntime::miDelef();
$mi_region = OrbixRuntime::miRegion();

$stackFromPost = isset($_POST['stack']) ? (int) filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;
$restored = ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
ListNavSupport::persistActaSelectReturnToPosicion($oPosicion, 0);

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qacta = (string)filter_input(INPUT_POST, 'acta');

ListNavSupport::persistSelectionOnListPage(
    $oPosicion,
    $Qid_sel,
    $Qscroll_id,
    $stackFromPost !== 0,
);

$oConfig = $_SESSION['oConfig'] ?? null;
$mesFinStgr = $oConfig instanceof ConfigSnapshot ? $oConfig->getMesFinStgr() : 6;

$d = PostRequest::getDataFromUrl('/src/notas/acta_select_data', [
    'titulo' => $Qtitulo,
    'acta' => $Qacta,
    'mes_fin_stgr' => $mesFinStgr,
]);
$presentacion = NotasPayload::actaSelectFromPayload($d);
$titulo = $presentacion['titulo'];
$a_asignaturas = $presentacion['a_asignaturas'];
$cActasData = $presentacion['actas'];

$botones = 0; // para 'añadir acta'
/** @var list<array{txt: string, click: string}> $a_botones */
$a_botones = [];
// Si soy region del stgr, no puedo modificar actas: que lo hagan las dl.
if (OrbixRuntime::miAmbito() === 'rstgr') {
    $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
    $botones = 0;
} else {
    $oPerm = $_SESSION['oPerm'] ?? null;
    if ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('est')) {
        $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
        $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
        $botones = 1; // para 'añadir acta'
    }
}

$a_botones[] = ['txt' => _("imprimir"), 'click' => "fnjs_imprimir(\"#seleccionados\")"];
$a_botones[] = ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];

/** @var list<array<string, mixed>|string> $a_cabeceras */
$a_cabeceras = [['name' => ucfirst(_("acta")), 'formatter' => 'clickFormatter'],
    ['name' => ucfirst(_("fecha")), 'class' => 'fecha'],
    _("asignatura"),
    _("firmada"),
];

$i = 0;
$a_valores = [];
/** @var array<string, string> mapa checkbox `sel` (= urlencode acta) → URL firmada de descarga */
$pdf_signed_urls = [];
foreach ($cActasData as $oActa) {
    $i++;
    $acta = $oActa['acta'];
    $f_acta = $oActa['f_acta'];
    $id_asignatura = $oActa['id_asignatura'];
    $hasPdf = $oActa['has_pdf'] ? _("Sí") : '';

    if (!isset($a_asignaturas[$id_asignatura]) || $a_asignaturas[$id_asignatura] === '') {
        $nombre_corto = sprintf(_("nombre corto no definido para id asignatura: %s"), $id_asignatura);
    } else {
        $nombre_corto = $a_asignaturas[$id_asignatura];
    }
    $acta_2 = urlencode($acta);
    /* Token HMAC (sin HashFront): evita redirección a inicio si emisor/receptor no coinciden en URL. */
    $pdf_signed_urls[$acta_2] = SignedDownloadToken::urlNotasActa($acta);
    $pagina = HashFront::link('frontend/notas/controller/acta_ver.php?' . http_build_query(array('acta' => $acta)));
    $a_valores[$i]['sel'] = $acta_2;
    $oPerm = $_SESSION['oPerm'] ?? null;
    if ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('est')) {
        $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $acta);
    } else {
        $a_valores[$i][1] = $acta;
    }
    $a_valores[$i][2] = $f_acta;
    $a_valores[$i][3] = $nombre_corto;
    $a_valores[$i][4] = $hasPdf;
}
if (!ListNavSupport::idSelIsEmpty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if ($Qscroll_id !== '') {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new HashFront();
$oHash->setCamposForm('acta');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh!id_sel');

$url_acta_eliminar = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/acta_eliminar';

$oTabla = new Lista();
$oTabla->setId_tabla('acta_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta");

$id_sel_value = is_array($Qid_sel) ? (string) ($Qid_sel[0] ?? '') : (string) $Qid_sel;

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'botones' => $botones,
    'txt_eliminar' => $txt_eliminar,
    'pdf_signed_urls_json' => json_encode($pdf_signed_urls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
    'url_acta_eliminar' => $url_acta_eliminar,
    'id_sel_value' => $id_sel_value,
];

$oView = new ViewNewPhtml('frontend\notas\controller');
$oView->renderizar('acta_select.phtml', $a_campos);
