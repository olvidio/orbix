<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\repositories\InicialesSacdRepository;
use personas\model\entity\PersonaSacd;
use personas\model\entity\PersonaEx;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $nom = $PersonaSacd->getNom();
        $ap1 = $PersonaSacd->getApellido1();
        $ap2 = $PersonaSacd->getApellido2();
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        $nom = $PersonaEx->getNom();
        $ap1 = $PersonaEx->getApellido1();
        $ap2 = $PersonaEx->getApellido2();
    }

    // iniciales
    $inom='';
    if (!is_null($nom))
        $inom = mb_substr($nom, 0, 1);
    $iap1='';
    if (!is_null($ap1))
        $iap1 = mb_substr($ap1, 0, 1);
    $iap2='';
    if (!is_null($ap2))
        $iap2 = mb_substr($ap2, 0, 1);

    $iniciales = strtoupper($inom . $iap1 . $iap2);
    return $iniciales;
}


$oGestorZona = new GestorZona();
$cZonas = $oGestorZona->getZonas(['_ordre' => 'orden']);

$gesZonaSacd = new GestorZonaSacd();
$a_zonas = [];
foreach ($cZonas as $oZona) {
    $id_zona = $oZona->getId_zona();
    $nombre_zona = $oZona->getNombre_zona();

    $a_Id_nom = $gesZonaSacd->getSacdsZona($id_zona);
    $a_datos_sacd = [];
    foreach ($a_Id_nom as $id_nom) {
        if ($id_nom>0) {
            $PersonaSacd = new PersonaSacd($id_nom);
            $sacd = $PersonaSacd->getNombreApellidos();
        } else {
            $PersonaEx = new PersonaEx($id_nom);
            $sacd = $PersonaEx->getNombreApellidos();
        }
        // iniciales
        $InicialesSacdRepository = new InicialesSacdRepository();
        $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
        if ($InicialesSacd === null) {
            $iniciales = iniciales($id_nom);
            $color = '?';
        } else {
            $iniciales = $InicialesSacd->getIniciales();
            $color = $InicialesSacd->getColor();
        }

        $a_datos_sacd[$id_nom] = ['nombre_sacd' => $sacd, 'iniciales' => $iniciales, 'color' => $color];
    }
    $a_zonas[$nombre_zona] = $a_datos_sacd;
}

$url_ver_plantilla_zona = 'apps/misas/controller/ver_plantilla_zona.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_ver_plantilla_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinVal();


$a_campos = ['oPosicion' => $oPosicion,
    'url_ver_plantilla_zona' => $url_ver_plantilla_zona,
    'h_zona' => $h_zona,
    'a_zonas' => $a_zonas,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('tabla_iniciales_sacd.html.twig', $a_campos);