<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;

require_once("frontend/shared/global_header_front.inc");

//En el caso de modificar cartas de presentación, quiero que quede dentro del bloque.
$oPosicion->recordar();
$bloque = (string)filter_input(INPUT_POST, 'bloque');
if (!empty($bloque)) {
    $oPosicion->setBloque("#$bloque");
    $oPosicion->addParametro('bloque', $bloque);
}
$bloque = 'ficha';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $obj_pau = $oPosicion2->getParametro('obj_pau');
            $id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

// el scroll id es de la página anterior, hay que guardarlo allí
if (!empty($a_sel)) { //vengo de un checkbox
    $id_ubi = (integer)strtok($a_sel[0], "#");
} else {
    $id_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
}

$data = PostRequest::getDataFromUrl('/src/ubis/home_ubis_data', ['id_ubi' => $id_ubi]);
$nombre_ubi = $data['nombre_ubi'];
$dl = $data['dl'];
$region = $data['region'];
$direccion = $data['direccion'];
$poblacion = $data['poblacion'];
$c_p = $data['c_p'];
$id_direccion = $data['id_direccion'];
$id_pau = $data['id_pau'];
$pau = $data['pau'];
$obj_pau = $data['obj_pau'];
$obj_dir = $data['obj_dir'];
$ubi = $data['ubi'];

$gohome = HashFront::link('frontend/ubis/controller/home_ubis.php?' . http_build_query(array('id_ubi' => $id_ubi, 'obj_pau' => $obj_pau)));
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query(array('pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $obj_pau)));

$go_ubi = HashFront::link('frontend/ubis/controller/ubis_editar.php?' . http_build_query(array('id_ubi' => $id_ubi, 'obj_pau' => $obj_pau, 'bloque' => $bloque)));
$go_dir = HashFront::link('frontend/ubis/controller/direcciones_editar.php?' . http_build_query(array('id_ubi' => $id_ubi, 'id_direccion' => $id_direccion, 'obj_dir' => $obj_dir, 'bloque' => $bloque)));
$go_tel = HashFront::link('frontend/ubis/controller/teleco_tabla.php?' . http_build_query(array('id_ubi' => $id_ubi, 'obj_pau' => $obj_pau, 'bloque' => $bloque)));

$alt = _("ver dossiers");
$dos = _("dossiers");
$txt = ucfirst(_("formato texto"));
$titulo = $nombre_ubi;

$telfs = $data['telfs'];
$fax = $data['fax'];
$mails = $data['mails'];

$a_campos = ['oPosicion' => $oPosicion,
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'gohome' => $gohome,
    'titulo' => $titulo,
    'dl' => $dl,
    'region' => $region,
    'direccion' => $direccion,
    'c_p' => $c_p,
    'poblacion' => $poblacion,
    'telfs' => $telfs,
    'fax' => $fax,
    'mails' => $mails,
    'go_ubi' => $go_ubi,
    'ubi' => $ubi,
    'go_dir' => $go_dir,
    'go_tel' => $go_tel,
    'pau' => $pau,
    'id_pau' => $id_pau,
    'Qobj_pau' => $obj_pau
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('home_ubis.phtml', $a_campos);