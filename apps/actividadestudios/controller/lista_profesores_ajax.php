<?php

use profesores\model\entity as profesores;

/*
* Devuelvo un desplegable con los valores posibles segun el valor de entrada.
*
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsalida = (string)\filter_input(INPUT_POST, 'salida');

switch ($Qsalida) {
    case "asignatura":
        $Qid_asignatura = (integer)\filter_input(INPUT_POST, 'id_asignatura');
        $GesProfesores = new profesores\GestorProfesor();
        $oDesplProfesores = $GesProfesores->getDesplProfesoresAsignatura($Qid_asignatura);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "dl":
        $Qid_activ = (integer)\filter_input(INPUT_POST, 'id_activ');
        $GesProfesores = new profesores\GestorProfesorActividad();
        $oDesplProfesores = $GesProfesores->getListaProfesoresActividad(array($Qid_activ));

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
    case "todos":
        $GesProfesores = new profesores\GestorProfesor();
        $aOpciones = $GesProfesores->getListaProfesoresPub();

        $oDesplProfesores = new web\Desplegable('', $aOpciones, '', true);

        $oDesplProfesores->setNombre('id_profesor');
        $oDesplProfesores->setBlanco('t');
        $oDesplProfesores->setOpcion_sel(-1);

        echo $oDesplProfesores->desplegable();
        break;
}
