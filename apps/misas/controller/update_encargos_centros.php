<?php

// INICIO Cabecera global de URL de controlador *********************************

use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_item = (string)filter_input(INPUT_POST, 'id_item');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');

//echo 'id_item: '.$Qid_item.'<br>';
//echo $Qid_enc.'-->'.$Qid_ctr.'<br>';

$error_txt = '';

if ($Qque === 'nuevo') {
    $Uuid = new EncargoCtrId(RamseyUuid::uuid4()->toString());
    $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
    $EncargoCtr = new EncargoCtr();
    $EncargoCtr->setUuid_item($Uuid);
    $EncargoCtr->setId_ubi($Qid_ctr);
    $EncargoCtr->setId_enc($Qid_enc);
    if ($EncargoCtrRepository->Guardar($EncargoCtr) === FALSE) {
        $error_txt .= $EncargoCtrRepository->getErrorTxt();
    }
}

if ($Qque === 'modificar') {
    $Uid_item = new EncargoCtrId($Qid_item);

    $EncargoCtr = new EncargoCtr();
    $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
    $EncargoCtr = $EncargoCtrRepository->findById($Uid_item);

    $EncargoCtr->setId_ubi($Qid_ctr);
    $EncargoCtr->setId_enc($Qid_enc);

    if ($EncargoCtrRepository->Guardar($EncargoCtr) === FALSE) {
        $error_txt .= $EncargoCtrRepository->getErrorTxt();
    }
}

if ($Qque === 'borrar') {
    $Uid_item = new EncargoCtrId($Qid_item);
    $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);
    $EncargoCtr = $EncargoCtrRepository->findById($Uid_item);
    if ($EncargoCtrRepository->Eliminar($EncargoCtr) === FALSE) {
        $error_txt .= $EncargoCtrRepository->getErrorTxt();
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}
(new JsonResponse($jsondata))->send();
exit();
