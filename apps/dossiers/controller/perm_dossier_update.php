<?php

use function core\is_true;
use dossiers\model\entity as dossiers;

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

switch ($Qque) {
    case 'eliminar':
        $oTipoDossier = new dossiers\TipoDossier($Qid_tipo_dossier);
        if ($oTipoDossier->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oTipoDossier->getErrorTxt();
        }
        echo $oPosicion->go_atras(1);
        die();
        break;
    case 'guardar':
        $oTipoDossier = new dossiers\TipoDossier($Qid_tipo_dossier);
        break;
}

$campos_chk = empty($Qcampos_chk) ? array() : explode('!', $Qcampos_chk);
$oTipoDossier->DBCarregar();
$oDbl = $oTipoDossier->getoDbl();
$cDatosCampo = $oTipoDossier->getDatosCampos();
foreach ($cDatosCampo as $oDatosCampo) {
    $camp = $oDatosCampo->getNom_camp();
    $valor = empty($_POST[$camp]) ? '' : $_POST[$camp];
    if ($oDatosCampo->datos_campo($oDbl, 'tipo') == "bool") { //si es un campo boolean, cambio los valores on, off... por true, false...
        if ($valor == "on") {
            $valor = 't';
            $a_values_o[$camp] = $valor;
        } else {
            // compruebo que esté en la lista de campos enviados
            if (in_array($camp, $campos_chk)) {
                $valor = 'f';
                $a_values_o[$camp] = $valor;
            }
        }
    } else {
        if (!isset($_POST[$camp]) && !empty($Qid_tipo_dossier)) continue; // sólo si no es nuevo

        //cuando el campo es permiso_lectura, se pasa un array que hay que convertirlo en número.
        if ($camp == "permiso_lectura") {
            $byte = 0;
            foreach ($_POST[$camp] as $bit) {
                $byte = $byte + $bit;
            }
            $valor = $byte;
        }
        //cuando el campo es permiso_escritura, se pasa un array que hay que convertirlo en número.
        if ($camp == "permiso_escritura") {
            $byte = 0;
            foreach ($_POST[$camp] as $bit) {
                $byte = $byte + $bit;
            }
            $valor = $byte;
        }
        //pongo el valor nulo, sobretodo para las fechas.
        if (!is_array($_POST[$camp]) && (empty($_POST[$camp]) || trim($_POST[$camp]) == "")) {
            //si es un campo not null (y es null), pongo el valor por defecto
            if (is_true($oDatosCampo->datos_campo($oDbl, 'nulo'))) {
                $valor_predeterminado = $oDatosCampo->datos_campo($oDbl, 'valor');
                $a_values_o[$camp] = $valor_predeterminado;
            } else {
                $a_values_o[$camp] = NULL;
            }
        } else {
            $a_values_o[$camp] = $valor;
        }
    }
}
$oTipoDossier->setAllAtributes($a_values_o, TRUE);
$oTipoDossier->DBGuardar();