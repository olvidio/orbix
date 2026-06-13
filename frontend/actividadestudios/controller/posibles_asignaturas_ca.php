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

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$sel = actividadestudios_id_activ_nom_from_sel_post();
$id_activ = $sel['id_activ'];
$nom_activ = $sel['nom_activ'];

$d = actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/posibles_asignaturas_ca_data', [
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
]));

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => tessera_imprimir_string($d['nom_activ'] ?? $nom_activ),
    'aAsignaturas_alumnos' => actividades_lista_datos($d['aAsignaturas_alumnos'] ?? []),
    'a_alumnos_fin_c' => actividades_lista_datos($d['a_alumnos_fin_c'] ?? []),
];

$oView = new ViewNewTwig('frontend/actividadestudios/controller');
$oView->renderizar('posibles_asignaturas_ca.html.twig', $a_campos);
