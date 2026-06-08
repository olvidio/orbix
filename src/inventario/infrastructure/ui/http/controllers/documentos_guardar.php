<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\is_true;

$Qdocumentos = input_string($_POST, 'documentos');
$Qchk_f_recibido = input_string($_POST, 'chk_f_recibido');
$Qf_recibido = input_string($_POST, 'f_recibido');
$Qchk_f_asignado = input_string($_POST, 'chk_f_asignado');
$Qf_asignado = input_string($_POST, 'f_asignado');
$Qchk_eliminado = input_string($_POST, 'chk_eliminado');
$Qeliminado = input_int($_POST, 'eliminado');
$Qchk_f_eliminado = input_string($_POST, 'chk_f_eliminado');
$Qf_eliminado = input_string($_POST, 'f_eliminado');
$Qchk_num_ini = input_string($_POST, 'chk_num_ini');
$Qnum_ini = input_string($_POST, 'num_ini');
$Qchk_num_fin = input_string($_POST, 'chk_num_fin');
$Qnum_fin = input_string($_POST, 'num_fin');

$error_txt = '';

if (!empty($Qdocumentos)) {
    $a_documentos = explode('#', $Qdocumentos);
    /** @var DocumentoRepositoryInterface $Repository */
$Repository = DependencyResolver::get(DocumentoRepositoryInterface::class);
    foreach ($a_documentos as $s_doc_key) {
        if ($s_doc_key === '') {
            continue;
        }
        $decoded = base64_decode($s_doc_key, true);
        if (!is_string($decoded) || $decoded === '') {
            continue;
        }
        $a_pkey = json_decode($decoded, true);
        if (!is_array($a_pkey)) {
            continue;
        }
        $rawId = $a_pkey['id_doc'] ?? $a_pkey[0] ?? null;
        if (!is_numeric($rawId)) {
            continue;
        }
        $id_doc = (int) $rawId;
        $oDocumento = $Repository->findById($id_doc);
        if ($oDocumento === null) {
            continue;
        }

        if (is_true($Qchk_f_recibido)) {
            if (empty($Qf_recibido)) {
                $oF_recibido = null;
            } else {
                $oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);
            }
            $oDocumento->setF_recibido($oF_recibido);
        }
        if (is_true($Qchk_f_asignado)) {
            if (empty($Qf_asignado)) {
                $oF_asignado = null;
            } else {
                $oF_asignado = DateTimeLocal::createFromLocal($Qf_asignado);
            }
            $oDocumento->setF_asignado($oF_asignado);
        }
        if (is_true($Qchk_eliminado)) {
            if ($Qeliminado === 1) {
                $oDocumento->setEliminado(TRUE);
            } else if ($Qeliminado === 2) {
                $oDocumento->setEliminado(false);
            }
        }
        if (is_true($Qchk_f_eliminado)) {
            if (empty($Qf_eliminado)) {
                $oF_eliminado = null;
            } else {
                $oF_eliminado = DateTimeLocal::createFromLocal($Qf_eliminado);
            }
            $oDocumento->setF_eliminado($oF_eliminado);
        }
        if (is_true($Qchk_num_ini)) {
            $oDocumento->setNum_ini($Qnum_ini !== '' && is_numeric($Qnum_ini) ? (int) $Qnum_ini : null);
        }
        if (is_true($Qchk_num_fin)) {
            $oDocumento->setNum_fin($Qnum_fin !== '' && is_numeric($Qnum_fin) ? (int) $Qnum_fin : null);
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

