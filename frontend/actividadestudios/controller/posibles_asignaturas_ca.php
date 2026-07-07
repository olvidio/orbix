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

$sel = ActividadestudiosPostInput::idActivNom();
$id_activ = $sel['id_activ'];
$nom_activ = $sel['nom_activ'];

$navState = [];
$aSel = ListNavSupport::selFromPost();
if ($aSel !== []) {
    $navState['sel'] = $aSel;
}
foreach (['queSel', 'mod', 'obj_pau', 'pau', 'permiso'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    $navState,
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
if ($id_activ > 0) {
    $navState['id_activ'] = $id_activ;
}

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $id_activ > 0 ? ['id_activ' => $id_activ] : [],
    $navState,
);

ListNavSupport::syncActividadSelectParentSelection($oPosicion);

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
