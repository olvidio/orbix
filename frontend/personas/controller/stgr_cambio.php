<?php

namespace frontend\personas\controller;

use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use web\Desplegable;
use web\Hash;
use web\Posicion;

/**
 * Formulario para cambiar el `nivel_stgr` de una persona.
 *
 * Migrado desde `apps/personas/controller/stgr_cambio.php` (slice 1).
 */
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
} else {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

if (empty($id_tabla)) {
    echo _("No existe la clase de la persona");
    die();
}

$resolver = new PersonaRepositoryResolver();
try {
    $repository = $resolver->repositorioPorIdTabla($id_tabla);
} catch (\InvalidArgumentException) {
    echo _("No existe la clase de la persona");
    die();
}

$oPersona = $repository->findById($id_nom);
if ($oPersona === null) {
    echo _("No se encuentra la persona");
    die();
}

$nom = $oPersona->getNombreApellidos();
$stgr = $oPersona->getNivel_stgr();

$oDespl = new Desplegable();
$oDespl->setNombre('nivel_stgr');
$oDespl->setOpciones(NivelStgrId::getArrayNivelStgr());
$oDespl->setOpcion_sel($stgr);
$oDespl->setBlanco(true);

$oHash = new Hash();
$oHash->setCamposForm('nivel_stgr');
$oHash->setArraycamposHidden([
    'id_tabla' => $id_tabla,
    'id_nom' => $id_nom,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nom' => $nom,
    'oDespl' => $oDespl,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('stgr_cambio.phtml', $a_campos);
