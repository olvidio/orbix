<?php

use src\shared\config\ConfigGlobal;
use src\shared\config\ConfigMagik;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'cabecera_pie_txt_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';

$Qcabecera = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'cabecera');
$QcabeceraB = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'cabeceraB');
$Qfirma = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'firma');
$Qpie = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pie');

$file = ConfigGlobal::$dir_web ."/data/inventario/cabecera_pie_textos.ini";
$Config = new ConfigMagik($file, true, true);
$Config->SYNCHRONIZE = false;

$Config->set("cabecera", $_POST['cabecera'], "texto_tipo");
$Config->set("cabeceraB", $_POST['cabeceraB'], "texto_tipo");
$Config->set("firma", $_POST['firma'], "texto_tipo");
$Config->set("pie", $_POST['pie'], "texto_tipo");

$Config->save($file);

$error_txt = implode(';' ,$Config->ERRORS);
// envía una Response
ContestarJson::enviar($error_txt, 'ok');