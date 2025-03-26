<?php

use inventario\domain\repositories\DocumentoRepository;
use web\ContestarJson;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qdocumentos = (string)filter_input(INPUT_POST, 'documentos');
$Qchk_f_recibido = (string)filter_input(INPUT_POST, 'chk_f_recibido');
$Qf_recibido = (string)filter_input(INPUT_POST, 'f_recibido');
$Qchk_f_asignado = (string)filter_input(INPUT_POST, 'chk_f_asignado');
$Qf_asignado = (string)filter_input(INPUT_POST, 'f_asignado');
$Qchk_eliminado = (string)filter_input(INPUT_POST, 'chk_eliminado');
$Qeliminado = (integer)filter_input(INPUT_POST, 'eliminado');
$Qchk_f_eliminado = (string)filter_input(INPUT_POST, 'chk_f_eliminado');
$Qf_eliminado = (string)filter_input(INPUT_POST, 'f_eliminado');
$Qchk_num_ini = (string)filter_input(INPUT_POST, 'chk_num_ini');
$Qnum_ini = (string)filter_input(INPUT_POST, 'num_ini');
$Qchk_num_fin = (string)filter_input(INPUT_POST, 'chk_num_fin');
$Qnum_fin = (string)filter_input(INPUT_POST, 'num_fin');

$error_txt = '';

if (!empty($Qdocumentos)) {
    $a_documentos = explode('#', $Qdocumentos);
    $Repository = new DocumentoRepository();
    foreach ($a_documentos as $s_pkey) {
        $s_pkey = strtok($s_pkey, '#');
        $a_pkey = unserialize(base64_decode($s_pkey));
        $id_doc = $a_pkey;
        //echo $id_doc;
        $oDocumento = $Repository->findById($id_doc);

        if (is_true($Qchk_f_recibido)) {
            if (empty($Qf_recibido)) {
                $oF_recibido = new NullDateTimeLocal();
            } else {
                $oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);
            }
            $oDocumento->setF_recibido($oF_recibido);
        }
        if (is_true($Qchk_f_asignado)) {
            if (empty($Qf_asignado)) {
                $oF_recibido = new NullDateTimeLocal();
            } else {
                $oF_asignado = DateTimeLocal::createFromLocal($Qf_asignado);
            }
            $oDocumento->setF_asignado($oF_asignado);
        }
        if (is_true($Qchk_eliminado)) {
            if ($Qeliminado === 1) {
                $oDocumento->setEliminado(TRUE);
            } else if ($Qeliminado === 2) {
                $oDocumento->setEliminado(FALSE);
            }
        }
        if (is_true($Qchk_f_eliminado)) {
            if (empty($Qf_eliminado)) {
                $oF_recibido = new NullDateTimeLocal();
            } else {
                $oF_eliminado = DateTimeLocal::createFromLocal($Qf_eliminado);
            }
            $oDocumento->setF_eliminado($oF_eliminado);
        }
        if (is_true($Qchk_num_ini)) {
            $oDocumento->setNum_ini($Qnum_ini);
        }
        if (is_true($Qchk_num_fin)) {
            $oDocumento->setNum_fin($Qnum_fin);
        }

        if ($Repository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $Repository->getErrorTxt();
        }
    }
} else {
    $error_txt = _("No ha seleccionado ningún documento");
}

ContestarJson::enviar($error_txt, 'ok');

