<?php

use core\ConfigGlobal;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use web\Lista;

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $a_cabeceras[1] = _("dl");
}

$a_cabeceras[2] = _("apellidos, nombre");
$a_cabeceras[3] = _("tipo");
$a_cabeceras[4] = _("lugar");
$a_cabeceras[5] = _("inicio");
$a_cabeceras[6] = _("fin");
$a_cabeceras[7] = _("organiza");

$ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
$a_nomProfesor = $ProfesorRepository->getArrayProfesoresConDl();

$p = 0;
$ProfesorCongresoRepository = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
$a_tiposCong = $ProfesorCongresoRepository->getArrayTiposCongreso();
$a_valores = [];
foreach ($a_nomProfesor as $id_nom => $aClave) {
    $ap_nom = $aClave['ap_nom'];
    $dl = $aClave['dl'];
    $cProfesorCongreso = $ProfesorCongresoRepository->getProfesorCongresos(['id_nom' => $id_nom]);

    foreach ($cProfesorCongreso as $oProfesorCongreso) {
        $p++;
        $tipo = empty($a_tiposCong[$oProfesorCongreso->getTipo()]) ? '' : $a_tiposCong[$oProfesorCongreso->getTipo()];
        $lugar = $oProfesorCongreso->getLugar();
        $inicio = $oProfesorCongreso->getF_ini()->getFromLocal();
        $fin = $oProfesorCongreso->getF_fin()->getFromLocal();
        $organiza = $oProfesorCongreso->getOrganiza();

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $a_valores[$p][1] = $dl;
        }

        $a_valores[$p][2] = $ap_nom;
        $a_valores[$p][3] = $tipo;
        $a_valores[$p][4] = $lugar;
        $a_valores[$p][5] = $inicio;
        $a_valores[$p][6] = $fin;
        $a_valores[$p][7] = $organiza;
    }
}

$oTabla = new Lista();
$oTabla->setId_tabla('tabla_congreso');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

echo $oTabla->mostrar_tabla();