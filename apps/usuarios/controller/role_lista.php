<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use menus\model\entity\GestorGrupMenu;
use menus\model\entity\GestorGrupMenuRole;
use usuarios\domain\rolesLista;
use usuarios\model\entity\GestorRole;
use usuarios\model\entity\Usuario;
use web\ContestarJson;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************


$jsondata = rolesLista::rolesLista();

// envía una Response
ContestarJson::send($jsondata);
