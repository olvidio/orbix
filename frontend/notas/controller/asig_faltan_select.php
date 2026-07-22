<?php

use frontend\notas\helpers\NotasPayload;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\helpers\PayloadCoercion;

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
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = ListNavSupport::mergeSelectionIntoReturnParametros([
    'numero' => $Qnumero,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'lista' => $Qlista,
], $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);

ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::mergeSelectionIntoReturnParametros([
        'numero' => $Qnumero,
        'b_c' => $Qb_c,
        'c1' => $Qc1,
        'c2' => $Qc2,
        'personas_n' => $Qpersonas_n,
        'personas_agd' => $Qpersonas_agd,
        'lista' => $Qlista,
    ], $Qid_sel, $Qscroll_id),
);

if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_n) && !\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_agd)) {
    exit(_("Debe marcar un grupo de personas (n o agd)"));
}

$tabla = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/notas/asig_faltan_select_data', [
    'numero' => $Qnumero,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'lista' => $Qlista,
]));
$presentacion = NotasPayload::asigFaltanTablaFromPayload($tabla);
$titulo = $presentacion['titulo'];
$obj_pau = $presentacion['obj_pau'];
$rowsRaw = $presentacion['rows'] ?? [];
$rows = is_array($rowsRaw) ? $rowsRaw : [];

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
foreach ($rows as $rowRaw) {
    $row = NotasPayload::asigFaltanRow($rowRaw);
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
if (!ListNavSupport::idSelIsEmpty($Qid_sel)) {
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
