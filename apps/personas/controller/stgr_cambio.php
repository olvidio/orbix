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
use src\shared\infrastructure\ProvidesRepositories;
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

if (empty($id_tabla)) {
    echo "No existe la clase de la persona";
    die();
}

$entityTypeByIdTabla = [
    'n' => 'PersonaN',
    'x' => 'PersonaNax',
    'a' => 'PersonaAgd',
    's' => 'PersonaS',
    'sssc' => 'PersonaSSSC',
    'cp_sss' => 'PersonaSSSC',
    'pn' => 'PersonaEx',
    'pa' => 'PersonaEx',
];

if (!isset($entityTypeByIdTabla[$id_tabla])) {
    echo "No existe la clase de la persona";
    die();
}

$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object
    {
        return $this->getRepository($entityType);
    }
};

try {
    $repository = $repositoryProvider->get($entityTypeByIdTabla[$id_tabla]);
} catch (\InvalidArgumentException) {
    echo "No existe la clase de la persona";
    die();
}

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
