<?php

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\domain\ProfesorActividad;
use web\Desplegable;

/*
* Devuelvo un desplegable con los valores posibles según el valor de entrada.
*
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsalida = (string)filter_input(INPUT_POST, 'salida');

switch ($Qsalida) {
    case "asignatura":
        $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
        $ProfesorAsignaturaService = $GLOBALS['container']->get(ProfesorAsignaturaService::class);
        $aOpciones = $ProfesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($Qid_asignatura));
        $oDesplProfesores = new Desplegable();
        $oDesplProfesores->setOpciones($aOpciones);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "dl":
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $ProfesorActividad = new ProfesorActividad();
        $aOpciones = $ProfesorActividad->getArrayProfesoresActividad(array($Qid_activ));
        $oDesplProfesores = new Desplegable();
        $oDesplProfesores->setOpciones($aOpciones);
        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "todos":
        $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
        $aOpciones = $ProfesorStgrService->getArrayProfesoresPub();

        $oDesplProfesores = new Desplegable('', $aOpciones, '', true);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
}
