<?php

use frontend\actividadestudios\helpers\ActividadestudiosPostInput;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

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

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootActividadSelectChildRecordar($oPosicion, $Qrefresh);
}
$sel = ActividadestudiosPostInput::idActivNom();
\frontend\shared\helpers\ListNavSupport::persistActividadSelectChildEntry(
    $oPosicion,
    $sel['id_activ'] > 0 ? ['id_activ' => $sel['id_activ']] : [],
);
$id_activ = $sel['id_activ'];
$nom_activ = $sel['nom_activ'];

$d = ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/posibles_asignaturas_ca_data', [
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ,
]));

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($d['nom_activ'] ?? $nom_activ),
    'aAsignaturas_alumnos' => ActividadesListaSupport::datos($d['aAsignaturas_alumnos'] ?? []),
    'a_alumnos_fin_c' => ActividadesListaSupport::datos($d['a_alumnos_fin_c'] ?? []),
];

$oView = new ViewNewTwig('frontend/actividadestudios/controller');
$oView->renderizar('posibles_asignaturas_ca.html.twig', $a_campos);
