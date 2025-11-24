<?php

use casas\model\entity\GrupoCasa;
use core\ViewTwig;
use src\ubis\application\repositories\CasaDlRepository;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

if (!empty($Qid_item)) {
    $oGrupoCasa = new GrupoCasa($Qid_item);
    $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
    $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
} else {
    $id_ubi_hijo = '';
    $id_ubi_padre = '';
}

$CasaDlRepository = new CasaDlRepository();
$aCasas = $CasaDlRepository->getArrayCasas("WHERE status = 't'");

$oDesplCasaMadre = new Desplegable('id_ubi_padre', $aCasas, $id_ubi_padre, '');
$oDesplCasaHija = new Desplegable('id_ubi_hijo', $aCasas, $id_ubi_hijo, '');


$url_ajax = 'apps/casas/controller/grupo_ajax.php';

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$a_camposHidden = [
    'que' => 'update',
    'id_item' => $Qid_item,
];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('id_ubi_padre!id_ubi_hijo');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'oDesplCasaMadre' => $oDesplCasaMadre,
    'oDesplCasaHija' => $oDesplCasaHija,
];

$oView = new ViewTwig('casas/controller');
$oView->renderizar('grupo_form.html.twig', $a_campos);