<?php
/**
 * Esta página sirve para realizar el cambio de stgr de una persona.
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ViewPhtml;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

switch ($id_tabla) {
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

// según sean numerarios...
$repository = new $repo;
$oPersona = $repository->findById($id_nom);

$nom = $oPersona->getNombreApellidos();
$stgr = $oPersona->getNivel_stgr();

//posibles valores de stgr
$NivelStgrRepository = $GLOBALS['container']->get(NivelStgrRepositoryInterface::class);
$aNivelStgr = $NivelStgrRepository->getArrayNivelesStgr();

$oDespl = new Desplegable();
$oDespl->setNombre('nivel_stgr');
$oDespl->setOpciones($aNivelStgr);
$oDespl->setOpcion_sel($stgr);
$oDespl->setBlanco(true);

$oHash = new Hash();
$oHash->setCamposForm('nivel_stgr');
$a_camposHidden = array(
    'id_tabla' => $id_tabla,
    'id_nom' => $id_nom
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nom' => $nom,
    'oDespl' => $oDespl,
];

$oView = new ViewPhtml('personas\controller');
$oView->renderizar('stgr_cambio.phtml', $a_campos);