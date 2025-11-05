<?php

use notas\model\entity\GestorPersonaNotaDB;

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


$id_nom_org = (integer)filter_input(INPUT_POST, 'id_nom_org');
$id_nom_dst = (integer)filter_input(INPUT_POST, 'id_nom_dst');

$gesPersonaNota = new GestorPersonaNotaDB();
$cPersonaOrgNotas = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom_org]);

$error = '';
foreach ($cPersonaOrgNotas as $oPersonaNota) {
    $oPersonaNota->DBCarregar();
    //print_r($oPersonaNota);
    $NuevoObj = clone $oPersonaNota;
    $NuevoObj->setId_nom($id_nom_dst);
    if ($NuevoObj->DBGuardar() === false) {
        $error .= '<br>' . _("no se ha guardado la nota");
    }
}

if (!empty($error)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
}

//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata, JSON_THROW_ON_ERROR);
exit();