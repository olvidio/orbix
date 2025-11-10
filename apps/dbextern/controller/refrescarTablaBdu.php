<?php

use core\ConfigGlobal;
use dbextern\model\CopiarBDU;
use dbextern\model\entity\GestorIdMatchPersona;
use dbextern\model\entity\zGestorPersonaListas;
use dbextern\model\entity\IdMatchPersona;
use dbextern\model\SincroDB;
use ubis\model\entity\GestorCentroDl;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = ConfigGlobal::mi_sfsv();

$que = (string)filter_input(INPUT_POST, 'que');

$CopiarBDU = new CopiarBDU();

$CopiarBDU->crearTablaTmp();