<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargoHorario;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\domain\entity\EncargoDia;
use misas\domain\repositories\EncargoDiaRepository;
use web\DateTimeLocal;
use ubis\model\entity\Ubi;
use misas\domain\repositories\InicialesSacdRepository;
use misas\domain\entity\InicialesSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_sacd = (string)filter_input(INPUT_POST, 'id_sacd');
$Qiniciales = (string)filter_input(INPUT_POST, 'iniciales');
$Qcolor = (string)filter_input(INPUT_POST, 'color');

echo 'echo: '.$Qid_sacd, $Qiniciales, $Qcolor.'<br>';

$error_txt = '';

$InicialesSacd = new InicialesSacd();
//$InicialesSacd = $InicialesSacdRepository->findById($Qid_sacd);
$inicialesSacd = $InicialesSacd->setId_nom($Qid_sacd);
$inicialesSacd = $InicialesSacd->setIniciales('MG');
//$inicialesSacd = $InicialesSacd->setIniciales($Qiniciales);
$color = $InicialesSacd->setColor($Qcolor);

//if ($InicialesSacd->DBGuardar($InicialesSacd) === FALSE) {
if ($InicialesSacd->Guardar($InicialesSacd) === FALSE) {
        echo 'ERROR'.$InicialesSacd->getErrorTxt();
    $error_txt .= $InicialesSacd->getErrorTxt();
}

//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();
