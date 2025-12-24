<?php

use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use function core\is_true;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_tipo_dossier = (integer)filter_input(INPUT_POST, 'id_tipo_dossier');
$Qcampos_chk = (string)filter_input(INPUT_POST, 'campos_chk');

$TipoDossierRepository = $GLOBALS['container']->get(TipoTelecoRepositoryInterface::class);
switch ($Qque) {
    case 'eliminar':
        $oTipoDossier = $TipoDossierRepository->findById($Qid_tipo_dossier);
        $TipoDossierRepository->Eliminar($oTipoDossier);
        echo $oPosicion->go_atras(1);
        die();
        break;
    case 'guardar':
        $oTipoDossier = $TipoDossierRepository->findById($Qid_tipo_dossier);

        $Qdescripcion = (string)filter_input(INPUT_POST, 'descripcion');
        $Qtabla_from = (string)filter_input(INPUT_POST, 'tabla_from');
        $Qtabla_to = (string)filter_input(INPUT_POST, 'tabla_to');
        $Qcampo_to = (string)filter_input(INPUT_POST, 'campo_to');
        $Qid_tipo_dossier_rel = (integer)filter_input(INPUT_POST, 'id_tipo_dossier_rel');
        $Qdepende_modificar = (string)filter_input(INPUT_POST, 'depende_modificar');
        $Qapp = (string)filter_input(INPUT_POST, 'app');
        $Qclass = (string)filter_input(INPUT_POST, 'class');
        $Qdb = (integer)filter_input(INPUT_POST, 'db');
        $aPermiso_lectura = (array)filter_input(INPUT_POST, 'Permiso_lectura', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $aPermiso_escritura = (array)filter_input(INPUT_POST, 'Permiso_escritura', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $oTipoDossier->setDescripcion($Qdescripcion);
        $oTipoDossier->setTabla_from($Qtabla_from);
        $oTipoDossier->setTabla_to($Qtabla_to);
        $oTipoDossier->setCampo_to($Qcampo_to);
        $oTipoDossier->setId_tipo_dossier_rel($Qid_tipo_dossier_rel);
        $oTipoDossier->setDepende_modificar(is_true($Qdepende_modificar));
        $oTipoDossier->setApp($Qapp);
        $oTipoDossier->setClass($Qclass);
        $oTipoDossier->setId_schema($Qdb);
        //cuando el campo es permiso_lectura, se pasa un array que hay que convertirlo en número.
        if (!empty($aPermiso_lectura) && (count($aPermiso_lectura) > 0)) {
            $byte = 0;
            foreach ($aPermiso_lectura as $bit) {
                $byte = $byte + $bit;
            }
            $valor = $byte;
            $oTipoDossier->setPermiso_lectura($valor);
        }
        //cuando el campo es permiso_escritura, se pasa un array que hay que convertirlo en número.
        if (!empty($aPermiso_escritura) && (count($aPermiso_escritura) > 0)) {
            $byte = 0;
            foreach ($aPermiso_escritura as $bit) {
                $byte = $byte + $bit;
            }
            $valor = $byte;
            $oTipoDossier->setPermiso_escritura($valor);
        }

        if ($TipoDossierRepository->Guardar($oTipoDossier) === false) {
            echo _("Hay un error, no se ha guardado.");
        }
        break;
}