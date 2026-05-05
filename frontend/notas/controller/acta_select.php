<?php

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

require_once 'frontend/shared/global_header_front.inc';

$mi_dele = OrbixRuntime::miDelef();
$mi_region = OrbixRuntime::miRegion();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qacta = (string)filter_input(INPUT_POST, 'acta');

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'titulo' => $Qtitulo,
    'acta' => $Qacta);
$oPosicion->setParametros($aGoBack, 1);

$d = PostRequest::getDataFromUrl('/src/notas/acta_select_data', [
    'titulo' => $Qtitulo,
    'acta' => $Qacta,
    'mes_fin_stgr' => (int)$_SESSION['oConfig']->getMesFinStgr(),
]);
$titulo = (string)($d['titulo'] ?? '');
$a_asignaturas = $d['a_asignaturas'] ?? [];
$cActasData = $d['actas'] ?? [];

$botones = 0; // para 'añadir acta'
$a_botones = [];
// Si soy region del stgr, no puedo modificar actas: que lo hagan las dl.
if (OrbixRuntime::miAmbito() === 'rstgr') {
    $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
    $botones = 0;
} else {
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
        $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
        $botones = 1; // para 'añadir acta'
    }
}

$a_botones[] = ['txt' => _("imprimir"), 'click' => "fnjs_imprimir(\"#seleccionados\")"];
$a_botones[] = ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];

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
    $acta = (string)($oActa['acta'] ?? '');
    $f_acta = $oActa['f_acta'] ?? null;
    $id_asignatura = (int)($oActa['id_asignatura'] ?? 0);
    $hasPdf = empty($oActa['has_pdf']) ? '' : _("Sí");

    if (empty($a_asignaturas[$id_asignatura])) {
        $nombre_corto = sprintf(_("nombre corto no definido para id asignatura: %s"), $id_asignatura);
    } else {
        $nombre_corto = $a_asignaturas[$id_asignatura];
    }
    $acta_2 = urlencode($acta);
    /* Token HMAC (sin HashFront): evita redirección a inicio si emisor/receptor no coinciden en URL. */
    $pdf_signed_urls[$acta_2] = SignedDownloadToken::urlNotasActa($acta);
    $pagina = HashFront::link('frontend/notas/controller/acta_ver.php?' . http_build_query(array('acta' => $acta)));
    $a_valores[$i]['sel'] = $acta_2;
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $acta);
    } else {
        $a_valores[$i][1] = $acta;
    }
    $a_valores[$i][2] = $f_acta;
    $a_valores[$i][3] = $nombre_corto;
    $a_valores[$i][4] = $hasPdf;
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new HashFront();
$oHash->setCamposForm('acta');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');

$url_acta_eliminar = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/acta_eliminar';

$oTabla = new Lista();
$oTabla->setId_tabla('acta_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta");

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'botones' => $botones,
    'txt_eliminar' => $txt_eliminar,
    'pdf_signed_urls_json' => json_encode($pdf_signed_urls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
    'url_acta_eliminar' => $url_acta_eliminar,
];

$oView = new ViewNewPhtml('frontend\notas\controller');
$oView->renderizar('acta_select.phtml', $a_campos);
