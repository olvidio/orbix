<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
$Qnivel_stgr = (string)filter_input(INPUT_POST, 'nivel_stgr');

// según sean numerarios...
switch ($Qid_tabla) {
    case 'n':
        $repo = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        break;
    case "x":
        $repo = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
        break;
    case "a":
        $repo = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
        break;
    case "s":
        $repo = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        break;
    case "cp_sss":
        $repo = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
        break;
    case "pn":
    case "pa":
        $repo = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        break;
}
$repository = new $repo;
$oPersona = $repository->findById($Qid_nom);

$oPersona->setNivel_stgr($Qnivel_stgr);
if ($repository->Guardar($oPersona) === false) {
    echo _("hay un error, no se ha guardado");
    echo "\n" . $oPersona->getErrorTxt();
}

echo $oPosicion->go_atras(1);