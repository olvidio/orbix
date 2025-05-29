<?php


use src\inventario\application\repositories\EgmRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\domain\ListaDocsGrupo;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$error_txt = '';

$LugarRepository = new LugarRepository();
$EgmRepository = new EgmRepository();
$cEgm = $EgmRepository->getEgmes(['id_equipaje' => $Qid_equipaje, '_ordre' => 'id_grupo']);
$id_grupo = 0;
$html_g = '';
$a_egm = [];
$i = 0;
foreach ($cEgm as $oEgm) {
    $i++;
    $id_grupo = $oEgm->getId_grupo();
    $a_egm[$i]['id_grupo'] = $id_grupo;
    $id_lugar = $oEgm->getId_lugar();
    $a_egm[$i]['id_lugar'] = $id_lugar;
    $texto = $oEgm->getTexto();
    $a_egm[$i]['texto'] = $texto;

    $oLugar = $LugarRepository->findById($id_lugar);
    $nom_lugar = $oLugar->getNom_lugar();
    $a_egm[$i]['nom_lugar'] = $nom_lugar;
    // lista_docs_grupo($Qid_equipaje, $id_lugar, $id_grupo);
    $datos = ListaDocsGrupo::lista_docs_grupo($Qid_equipaje, $id_lugar, $id_grupo);

    $a_egm[$i]['a_valores'] = $datos['a_valores'];
    $a_egm[$i]['id_item_egm'] = $datos['id_item_egm'];
}


$data = [
    'a_egm' => $a_egm,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);