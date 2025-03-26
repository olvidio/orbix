<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/lista_casas_posibles_periodo.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$aCamposHidden = [
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'incio' => $Qinicio,
    'fin' => $Qfin,
];
$oHash->setArrayCamposHidden($aCamposHidden);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];

$oDesplUbis = new Desplegable();
$oDesplUbis->setNombre('id_cdc');
$oDesplUbis->setOpciones($a_opciones);
$oDesplUbis->setBlanco(TRUE);
$oDesplUbis->setAction('fnjs_ver_actividades_casa()');
echo $oDesplUbis->desplegable();


