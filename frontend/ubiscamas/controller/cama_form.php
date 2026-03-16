<?php

use frontend\shared\model\ViewNewPhtml;
use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

//$oPosicion->recordar($Qrefresh);

$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
// hace falta la habitación cuando es para una nueva cama
$Qid_habitacion = (integer)filter_input(INPUT_POST, 'id_habitacion');

$uuid_cama = CamaId::fromNullableString($Qid_cama);
$descripcion = '';
$larga = false;
$vip = false;

if ($uuid_cama === null) {
    //  nueva cama
    $Qid_cama = Uuid::uuid4()->toString();
} else {
    $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
    $oCama = $CamaRepository->findById($uuid_cama);
    $Qid_habitacion = $oCama->getIdHabitacionVo()->value();
    $descripcion = $oCama->getDescripcion() ?? '';
    $larga = $oCama->isLarga() ?? false;
    $vip = $oCama->isVip() ?? false;
}

$oHash = new Hash();
$camposForm = 'descripcion!larga!vip';
$camposChk = 'larga!vip';

$oHash->setCamposForm($camposForm);
$oHash->setCamposChk($camposChk);
$a_camposHidden = array(
    'id_cama' => $Qid_cama,
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'mod' => $Qmod,
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_cama' => $Qid_cama,
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'descripcion' => $descripcion,
    'larga' => $larga,
    'vip' => $vip,
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('cama_form.phtml', $a_campos);
