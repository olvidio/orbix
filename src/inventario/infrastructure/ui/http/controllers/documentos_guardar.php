<?php

declare(strict_types=1);

use src\inventario\application\support\DocumentoTablaSelKey;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
$Qdocumentos = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'documentos');
$Qchk_f_recibido = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_f_recibido');
$Qf_recibido = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_recibido');
$Qchk_f_asignado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_f_asignado');
$Qf_asignado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_asignado');
$Qchk_eliminado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_eliminado');
$Qeliminado = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'eliminado');
$Qchk_f_eliminado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_f_eliminado');
$Qf_eliminado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_eliminado');
$Qchk_num_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_num_ini');
$Qnum_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_ini');
$Qchk_num_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'chk_num_fin');
$Qnum_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_fin');

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'documentos_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';

$anyFieldToUpdate = FuncTablasSupport::isTrue($Qchk_f_recibido) === true
    || FuncTablasSupport::isTrue($Qchk_f_asignado) === true
    || FuncTablasSupport::isTrue($Qchk_eliminado) === true
    || FuncTablasSupport::isTrue($Qchk_f_eliminado) === true
    || FuncTablasSupport::isTrue($Qchk_num_ini) === true
    || FuncTablasSupport::isTrue($Qchk_num_fin) === true;

if (!empty($Qdocumentos)) {
    if (!$anyFieldToUpdate) {
        $error_txt = _('Debe marcar al menos un campo a modificar');
    } else {
    $a_documentos = explode('#', $Qdocumentos);
    /** @var DocumentoRepositoryInterface $Repository */
    $Repository = DependencyResolver::get(DocumentoRepositoryInterface::class);
    $processed = 0;
    foreach ($a_documentos as $s_doc_key) {
        if ($s_doc_key === '') {
            continue;
        }
        $id_doc = DocumentoTablaSelKey::idDocFromUrlsafeKey($s_doc_key);
        if ($id_doc === null) {
            continue;
        }
        $oDocumento = $Repository->findById($id_doc);
        if ($oDocumento === null) {
            continue;
        }

        if (FuncTablasSupport::isTrue($Qchk_f_recibido)) {
            if (empty($Qf_recibido)) {
                $oF_recibido = null;
            } else {
                $rawF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);
                $oF_recibido = $rawF_recibido instanceof DateTimeLocal ? $rawF_recibido : null;
            }
            $oDocumento->setF_recibido($oF_recibido);
        }
        if (FuncTablasSupport::isTrue($Qchk_f_asignado)) {
            if (empty($Qf_asignado)) {
                $oF_asignado = null;
            } else {
                $rawF_asignado = DateTimeLocal::createFromLocal($Qf_asignado);
                $oF_asignado = $rawF_asignado instanceof DateTimeLocal ? $rawF_asignado : null;
            }
            $oDocumento->setF_asignado($oF_asignado);
        }
        if (FuncTablasSupport::isTrue($Qchk_eliminado)) {
            if ($Qeliminado === 1) {
                $oDocumento->setEliminado(TRUE);
            } else if ($Qeliminado === 2) {
                $oDocumento->setEliminado(false);
            }
        }
        if (FuncTablasSupport::isTrue($Qchk_f_eliminado)) {
            if (empty($Qf_eliminado)) {
                $oF_eliminado = null;
            } else {
                $rawF_eliminado = DateTimeLocal::createFromLocal($Qf_eliminado);
                $oF_eliminado = $rawF_eliminado instanceof DateTimeLocal ? $rawF_eliminado : null;
            }
            $oDocumento->setF_eliminado($oF_eliminado);
        }
        if (FuncTablasSupport::isTrue($Qchk_num_ini)) {
            $oDocumento->setNum_ini($Qnum_ini !== '' && is_numeric($Qnum_ini) ? (int) $Qnum_ini : null);
        }
        if (FuncTablasSupport::isTrue($Qchk_num_fin)) {
            $oDocumento->setNum_fin($Qnum_fin !== '' && is_numeric($Qnum_fin) ? (int) $Qnum_fin : null);
        }

        if ($Repository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $Repository->getErrorTxt();
        } else {
            $processed++;
        }
    }
    if ($error_txt === '' && $processed === 0) {
        $error_txt = _('No se pudo aplicar la modificación a los documentos seleccionados');
    }
    }
} else {
    $error_txt = _("No ha seleccionado ningún documento");
}

ContestarJson::enviar($error_txt, 'ok');

