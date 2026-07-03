<?php

use frontend\notas\helpers\NotasPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$Qnumero = (int)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$Qlista = (string)filter_input(INPUT_POST, 'lista');

/** @var string|list<string> $Qid_sel */
$Qid_sel = \frontend\shared\helpers\ListNavSupport::idSelFromPost();
$Qscroll_id = \frontend\shared\helpers\ListNavSupport::scrollIdFromPost();

$stackFromPost = isset($_POST['stack']) ? (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;
if ($stackFromPost !== 0) {
    $oPosicion2 = new frontend\shared\web\Posicion();
    if ($oPosicion2->goStack($stackFromPost)) {
        $restoredSel = \frontend\shared\helpers\ListNavSupport::idSelForLista($oPosicion2->getParametro('id_sel'));
        if (!\frontend\shared\helpers\ListNavSupport::idSelIsEmpty($restoredSel)) {
            $Qid_sel = $restoredSel;
        }
        $restoredScroll = $oPosicion2->getParametro('scroll_id');
        if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
            $Qscroll_id = (string) $restoredScroll;
        }
        $Qnumero = \frontend\shared\helpers\PayloadCoercion::int($oPosicion2->getParametro('numero'), $Qnumero);
        $Qb_c = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('b_c') ?? $Qb_c);
        $Qc1 = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('c1') ?? $Qc1);
        $Qc2 = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('c2') ?? $Qc2);
        $Qpersonas_n = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('personas_n') ?? $Qpersonas_n);
        $Qpersonas_agd = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('personas_agd') ?? $Qpersonas_agd);
        $Qlista = \frontend\shared\helpers\PayloadCoercion::string($oPosicion2->getParametro('lista') ?? $Qlista);
        $oPosicion2->olvidar($stackFromPost);
    }
}

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros([
    'numero' => $Qnumero,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'lista' => $Qlista,
], $Qid_sel, $Qscroll_id));


$oPosicion->setParametros([
    'numero' => $Qnumero,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'lista' => $Qlista,
], 1);

\frontend\shared\helpers\ListNavSupport::persistSelectionOnListPage(
    $oPosicion,
    $Qid_sel,
    $Qscroll_id,
    $stackFromPost !== 0,
);

if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_n) && !\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_agd)) {
    exit(_("Debe marcar un grupo de personas (n o agd)"));
}

$tabla = PostRequest::getDataFromUrl('/src/notas/asig_faltan_select_data', [
    'numero' => $Qnumero,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'lista' => $Qlista,
]);
$presentacion = NotasPayload::asigFaltanTablaFromPayload($tabla);
$titulo = $presentacion['titulo'];
$obj_pau = $presentacion['obj_pau'];
$rows = $presentacion['rows'];

/** @var list<array{txt: string, click: string}> $a_botones */
$a_botones = NotasPayload::botonesModificarTessera();

/** @var list<array<string, mixed>|string> $a_cabeceras */
$a_cabeceras = [
    ucfirst(_("tipo")),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    ucfirst(_("centro")),
    ucfirst(_("stgr")),
    ucfirst(_("asignaturas")),
    ['name' => _("telf."), 'width' => 80],
    ['name' => _("mails"), 'width' => 100],
];

$i = 0;
$a_valores = [];
foreach ($rows as $row) {
    $i++;
    $id_nom = $row['id_nom'];
    $id_tabla = $row['id_tabla'];
    $nom = $row['nom'];
    $nombre_ubi = $row['nombre_ubi'];
    $stgr = $row['stgr'];
    $as = $row['asig_txt'];
    $telfs = $row['telfs'];
    $mails = $row['mails'];

    $pagina = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query(['id_nom' => $id_nom, 'obj_pau' => $obj_pau]));

    $a_valores[$i]['sel'] = "$id_nom#$id_tabla";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = ['ira' => $pagina, 'valor' => $nom];
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $stgr;
    $a_valores[$i][5] = $as;
    $a_valores[$i][6] = $telfs;
    $a_valores[$i][7] = $mails;
}
if (!\frontend\shared\helpers\ListNavSupport::idSelIsEmpty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if ($Qscroll_id !== '') {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setCamposNo('sel!scroll_id!id_sel');
$oHash->setArraycamposHidden(['pau' => 'p', 'obj_pau' => $obj_pau]);

$id_sel_value = is_array($Qid_sel) ? (string) ($Qid_sel[0] ?? '') : (string) $Qid_sel;

$oTabla = new Lista();
$oTabla->setId_tabla('asig_faltan_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'id_sel_value' => $id_sel_value,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asig_faltan_select.phtml', $a_campos);
