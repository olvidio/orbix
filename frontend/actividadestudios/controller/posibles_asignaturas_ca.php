<?php

/**
 * Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        5/3/03.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;

require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************


$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $parts = explode('#', $a_sel[0]);
    $id_activ = (int)($parts[0] ?? 0);
    $nom_activ = (string)($parts[1] ?? '');
}

$d = PostRequest::getDataFromUrl('/src/actividadestudios/posibles_asignaturas_ca_data', [
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
]);

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $d['nom_activ'] ?? '',
    'aAsignaturas_alumnos' => $d['aAsignaturas_alumnos'] ?? [],
    'a_alumnos_fin_c' => $d['a_alumnos_fin_c'] ?? [],
];

$oView = new ViewNewTwig('actividadestudios/controller');
$oView->renderizar('posibles_asignaturas_ca.html.twig', $a_campos);
