<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qnumero = (int)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$Qlista = (string)filter_input(INPUT_POST, 'lista');
$Qid_sel = '';

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!is_true($Qpersonas_n) && !is_true($Qpersonas_agd)) {
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
$presentacion = notas_asig_faltan_tabla_from_payload($tabla);
$titulo = $presentacion['titulo'];
$obj_pau = $presentacion['obj_pau'];
$rows = $presentacion['rows'];

/** @var list<array{txt: string, click: string}> $a_botones */
$a_botones = notas_botones_modificar_tessera();

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
if ($a_valores !== [] && $Qid_sel !== '') {
    $a_valores['select'] = $Qid_sel;
}

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArraycamposHidden(['pau' => 'p', 'obj_pau' => $obj_pau]);

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
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asig_faltan_select.phtml', $a_campos);
