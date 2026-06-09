<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$Qid_sel = '';

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!is_true($Qpersonas_n) && !is_true($Qpersonas_agd)) {
    exit(_("Debe marcar un grupo de personas (n o agd)"));
}

/** @var list<array{txt: string, click: string}> $a_botones */
$a_botones = notas_botones_modificar_tessera();

/** @var list<array<string, mixed>|string> $a_cabeceras */
$a_cabeceras = [
    ucfirst(_("tipo")),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    ucfirst(_("centro")),
    ucfirst(_("stgr")),
    ['name' => _("telf."), 'width' => 80],
    ['name' => _("mails"), 'width' => 100],
];

$tabla = PostRequest::getDataFromUrl('/src/notas/asig_faltan_personas_select_data', [
    'id_asignatura' => $Qid_asignatura,
    'personas_n' => $Qpersonas_n,
    'personas_agd' => $Qpersonas_agd,
    'b_c' => $Qb_c,
    'c1' => $Qc1,
    'c2' => $Qc2,
]);
$presentacion = notas_asig_faltan_tabla_from_payload($tabla);
$titulo = $presentacion['titulo'];
$obj_pau = $presentacion['obj_pau'];
$rows = $presentacion['rows'];

$i = 0;
$a_valores = [];
if ($Qid_sel !== '') {
    $a_valores['select'] = $Qid_sel;
}
foreach ($rows as $row) {
    $i++;
    $id_nom = $row['id_nom'];
    $id_tabla = $row['id_tabla'];
    $nom = $row['nom'];
    $nombre_ubi = $row['nombre_ubi'];
    $stgr = $row['stgr'];
    $telfs = $row['telfs'];
    $mails = $row['mails'];

    $pagina = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query(['id_nom' => $id_nom, 'obj_pau' => $obj_pau]));

    $a_valores[$i] = [
        'sel' => "$id_nom#$id_tabla",
        1 => $id_tabla,
        2 => ['ira' => $pagina, 'valor' => $nom],
        3 => $nombre_ubi,
        4 => $stgr,
        5 => $telfs,
        6 => $mails,
    ];
}
$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArraycamposHidden(['pau' => 'p', 'obj_pau' => $obj_pau]);

$oTabla = new Lista();
$oTabla->setId_tabla('asig_faltan_personas_select');
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
$oView->renderizar('asig_faltan_personas_select.phtml', $a_campos);
