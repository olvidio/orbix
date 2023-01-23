<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\GestorPersonaSacd;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$gestorPersonaSacd = new GestorPersonaSacd();

$aWhere = [ 'situacion' => 'A',
    ];
$aOperador = ['situacion' => '=',
    ];

$cPersonaSacd = $gestorPersonaSacd->getPersonas($aWhere, $aOperador);

$a_cabeceras = ['nom', 'apellido1', 'apellido2'];
$a_valores = [];
$i = 0;
foreach ($cPersonaSacd as $PersonaSacd) {
    $i++;
    $a_valores[$i][0] = $PersonaSacd->getNom();
    $a_valores[$i][1] = $PersonaSacd->getApellido1();
    $a_valores[$i][2] = $PersonaSacd->getApellido2();
}

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
		'oTabla' => $oTabla,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('lista_sacd.html.twig', $a_campos);