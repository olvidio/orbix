<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\profesores\domain\InfoProfesorAmpliacion;
use src\profesores\domain\InfoProfesorCongreso;
use src\profesores\domain\InfoProfesorDirector;
use src\profesores\domain\InfoProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorJuramento;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\InfoProfesorStgr;
use src\profesores\domain\InfoProfesorTituloEst;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], "#");
    $Qid_tabla = (string)strtok("#");
} else {
    $Qid_pau = (int)filter_input(INPUT_POST, 'id_pau');
    $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_nom = empty($Qid_nom) ? $Qid_pau : $Qid_nom;
    $Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$stack = (string)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
if ($stack !== '') {
    $oPosicion2 = new web\Posicion();
    if ($oPosicion2->goStack($stack)) {
        $oPosicion2->olvidar($stack);
    }
}

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qprint = (int)filter_input(INPUT_POST, 'print');

if ($_SESSION['oPerm']->have_perm_oficina('est')) {
    $Qpermiso = '3';
}
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $Qprint = 1;
}

$url_backend = '/src/profesores/ficha_profesor_stgr';
$a_campos_backend = [
    'id_nom' => $id_nom,
    'id_tabla' => $Qid_tabla,
    'print' => $Qprint,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (!empty($data['error'])) {
    exit($data['error']);
}

$go_to = Hash::link(ConfigGlobal::getWeb() . '/frontend/profesores/controller/ficha_profesor_stgr.php?' . http_build_query([
    'id_nom' => $id_nom,
    'id_tabla' => $Qid_tabla,
    'permiso' => $Qpermiso,
    'depende' => $Qdepende,
]));

$go_cosas['print'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/profesores/controller/ficha_profesor_stgr.php?' . http_build_query([
    'id_nom' => $id_nom,
    'id_tabla' => $Qid_tabla,
    'print' => '1',
]));

$a_cosas = [
    'clase_info' => urlencode(InfoProfesorLatin::class),
    'pau' => 'p',
    'id_pau' => $id_nom,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
    'depende' => $Qdepende,
    'go_to' => $go_to,
];
$go_cosas['latin'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorTituloEst::class;
$go_cosas['curriculum'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorStgr::class;
$go_cosas['nombramientos'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorAmpliacion::class;
$go_cosas['ampliacion'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorCongreso::class;
$go_cosas['congresos'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorDocenciaStgr::class;
$go_cosas['docencia'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorDirector::class;
$go_cosas['director'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorJuramento::class;
$go_cosas['juramento'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

$a_cosas['clase_info'] = InfoProfesorPublicacion::class;
$go_cosas['publicaciones'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

echo $oPosicion->mostrar_left_slide(1);

$a_campos = array_merge($data, ['go_cosas' => $go_cosas]);
$oView = new ViewNewPhtml('frontend\profesores\controller');
if (!empty($Qprint)) {
    $oView->renderizar('ficha_profesor_stgr.print.phtml', $a_campos);
} else {
    $oView->renderizar('ficha_profesor_stgr.phtml', $a_campos);
}
