<?php 

use actividades\model\entity\Actividad;
use asistentes\model\entity\GestorAsistente;

/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_pau = (integer) strtok($a_sel[0],"#");
    $nom_activ=strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
    $id_pau = (integer) \filter_input(INPUT_POST, 'id_pau');
    $oActividad = new Actividad($id_pau);
    $nom_activ = $oActividad->getNom_activ();
}

$queSel = (string) \filter_input(INPUT_POST, 'queSel');
$gesAsistentes = new GestorAsistente();

echo "HOLA";