<?php

use profesores\model\entity\GestorProfesorActividad;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
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
        $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $aOpciones = $ProfesorRepository->getArrayTodosProfesoresAsignatura($Qid_asignatura);
        $oDesplProfesores = new Desplegable();
        $oDesplProfesores->setOpciones($aOpciones);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "dl":
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $GesProfesores = new GestorProfesorActividad();
        $oDesplProfesores = $GesProfesores->getListaProfesoresActividad(array($Qid_activ));

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "todos":
        $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $aOpciones = $ProfesorRepository->getArrayProfesoresPub();

        $oDesplProfesores = new Desplegable('', $aOpciones, '', true);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
}
