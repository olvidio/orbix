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
    case "n":
        $obj_pau = "PersonaN";
        break;
    case "x":
        $obj_pau = "PersonaNax";
        break;
    case "a":
        $obj_pau = "PersonaAgd";
        break;
    case "s":
        $obj_pau = "PersonaS";
        break;
    case "cp_sss":
        $obj_pau = "PersonaSSSC";
        break;
    case "pn":
    case "pa":
        $obj_pau = "PersonaEx";
        break;
}


// según sean numerarios...
$obj = 'personas\\model\\entity\\' . $obj_pau;
$oPersona = new $obj($id_nom);

$nom = $oPersona->getNombreApellidos();
$stgr = $oPersona->getStgr();

//posibles valores de stgr
$tipos = array("n" => _("no cursa est."),
    "b" => _("bienio"),
    "c1" => _("cuadrienio año I"),
    "c2" => _("cuadrienio año II-IV"),
    "r" => _("repaso"),
);

$oDespl = new web\Desplegable();
$oDespl->setNombre('stgr');
$oDespl->setOpciones($tipos);
$oDespl->setOpcion_sel($stgr);
$oDespl->setBlanco(true);

$oHash = new web\Hash();
$oHash->setcamposForm('stgr');
$a_camposHidden = array(
    'obj_pau' => $obj_pau,
    'id_nom' => $id_nom
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nom' => $nom,
    'oDespl' => $oDespl,
];

$oView = new core\View('personas/controller');
echo $oView->render('stgr_cambio.phtml', $a_campos);