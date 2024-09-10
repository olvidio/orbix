<?php

use notas\model\EditarPersonaNota;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';

$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

if ($Qpau !== "p") {
    exit ("OJO: pau no es de persona");
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nivel = (integer)strtok($a_sel[0], "#");
    $id_asignatura = (integer)strtok("#");
} else {
    $id_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
    $id_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');
}

$oEditarPersonaNota = new EditarPersonaNota($Qid_pau, $id_asignatura, $id_nivel);

switch ($Qmod) {
    case 'eliminar': //------------ BORRAR --------
        $msg_err = $oEditarPersonaNota->eliminar();
        break;
    case 'nuevo': //------------ NUEVO --------

        $camposExtra['id_situacion'] = (integer)filter_input(INPUT_POST, 'id_situacion');
        $camposExtra['acta'] = (string)filter_input(INPUT_POST, 'acta');
        $camposExtra['f_acta'] = (string)filter_input(INPUT_POST, 'f_acta');
        $camposExtra['tipo_acta'] = (integer)filter_input(INPUT_POST, 'tipo_acta');
        $camposExtra['preceptor'] = (string)filter_input(INPUT_POST, 'preceptor');
        $camposExtra['id_preceptor'] = (integer)filter_input(INPUT_POST, 'id_preceptor');
        $camposExtra['detalle'] = (string)filter_input(INPUT_POST, 'detalle');
        $camposExtra['epoca'] = (integer)filter_input(INPUT_POST, 'epoca');
        $camposExtra['id_activ'] = (integer)filter_input(INPUT_POST, 'id_activ');
        $camposExtra['nota_num'] = (float)filter_input(INPUT_POST, 'nota_num', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $camposExtra['nota_max'] = (integer)filter_input(INPUT_POST, 'nota_max');

        $oEditarPersonaNota->nuevo($camposExtra);
        break;
    case 'editar':  //------------ EDITAR --------
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él

        $camposExtra['id_situacion'] = (integer)filter_input(INPUT_POST, 'id_situacion');
        $camposExtra['acta'] = (string)filter_input(INPUT_POST, 'acta');
        $camposExtra['f_acta'] = (string)filter_input(INPUT_POST, 'f_acta');
        $camposExtra['tipo_acta'] = (integer)filter_input(INPUT_POST, 'tipo_acta');
        $camposExtra['preceptor'] = (string)filter_input(INPUT_POST, 'preceptor');
        $camposExtra['id_preceptor'] = (integer)filter_input(INPUT_POST, 'id_preceptor');
        $camposExtra['detalle'] = (string)filter_input(INPUT_POST, 'detalle');
        $camposExtra['epoca'] = (integer)filter_input(INPUT_POST, 'epoca');
        $camposExtra['id_activ'] = (integer)filter_input(INPUT_POST, 'id_activ');
        $camposExtra['nota_num'] = (float)filter_input(INPUT_POST, 'nota_num', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $camposExtra['nota_max'] = (integer)filter_input(INPUT_POST, 'nota_max');
        $camposExtra['id_asignatura_real'] = (integer)filter_input(INPUT_POST, 'id_asignatura_real');

        $msg_err = $oEditarPersonaNota->editar($camposExtra);
        break;
}


if (!empty($msg_err)) {
    echo $msg_err;
}
