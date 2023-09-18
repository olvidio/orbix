<?php

use personas\model\entity\GestorPersonaAgd;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaN;
use personas\model\entity\PersonaAgd;
use personas\model\entity\PersonaN;
use web\Desplegable;

/**
 * Esta página sirve para la tessera de una persona.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        22/11/02.
 *
 */

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

echo "<script>fnjs_left_side_hide()</script>";


//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

// no utilizo las búsquedas predefinidas, porque normalmente será una situación
// de 'Baja', y por defecto se busca en 'Altas'
$tipo_persona = 'n';
$oPersonaOrg = new PersonaN($id_nom);
if (!is_object($oPersonaOrg)) {
    // será agd
    $oPersonaOrg = new PersonaAgd($id_nom);
    $tipo_persona = 'agd';
    if (!is_object($oPersonaOrg)) {
        $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        exit($msg_err);
    }
}
$oPersonaOrg->DBCarregar();
$nom = $oPersonaOrg->getNombreApellidos();
$apellido1 = $oPersonaOrg->getApellido1();

if ($tipo_persona === 'n') {
    $gesPersonas = new GestorPersonaAgd();
}
if ($tipo_persona === 'agd') {
    $gesPersonas = new GestorPersonaN();
}
// no filtro por situación
// si excluyo el origen
$aWhere = ['apellido1' => $apellido1];
$cPersonas = $gesPersonas->getPersonas($aWhere);
$a_posibles_personas = [];
foreach ($cPersonas as $oPersona) {
    $id_nom_org = $oPersona->getId_nom();
    $nom_org = $oPersona->getNombreApellidos();
    $a_posibles_personas[$id_nom_org] = $nom_org;
}

$oDesplPersonas = new Desplegable();
$oDesplPersonas->setNombre('id_nom_org');
$oDesplPersonas->setBlanco('true');
$oDesplPersonas->setOpciones($a_posibles_personas);

$oHash = new web\Hash();
$oHash->setCamposForm('id_nom_org');
$a_camposHidden = array(
    'id_nom' => $id_nom
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = [
    'oPosicion' => $oPosicion,
    'nom' => $nom,
    'oDesplPersonas' => $oDesplPersonas,
    'oHash' => $oHash,

];


$oView = new core\ViewTwig('notas/controller');
$oView->renderizar('tessera_copiar_select.html.twig', $a_campos);