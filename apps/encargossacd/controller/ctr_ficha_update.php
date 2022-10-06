<?php

use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoTipo;
use ubis\model\entity\CentroDl;
use ubis\model\entity\CentroEllas;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoTipo;
use web\DateTimeLocal;

/**
 * Esta página actualiza la base de datos de los encargos.
 *
 * Se le puede pasar la varaible $mod.
 *    Si es 1 >> inserta un nuevo encargo.
 *    Si es 2 >> modifica
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        3/1/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qe = (integer)filter_input(INPUT_POST, 'e');
$Qmod = (string)filter_input(INPUT_POST, "mod_$Qe");
$Qid_enc = (integer)filter_input(INPUT_POST, "id_enc_$Qe");
$Qsacd_num = (integer)filter_input(INPUT_POST, "sacd_num");
$Qid_ubi = (integer)filter_input(INPUT_POST, "id_ubi_$Qe");
$Qtipo_centro = (string)filter_input(INPUT_POST, "tipo_centro_$Qe");
// Ahora mismo todo es por modulos
//$Qmod_horario = (string)  filter_input(INPUT_POST, "mod_horario $Qe");

$Qn_sacd = (integer)filter_input(INPUT_POST, 'n_sacd');
$Qn_sacd = empty($Qn_sacd) ? 1 : $Qn_sacd;

$Qid_sacd_titular = (integer)filter_input(INPUT_POST, 'id_sacd_titular');
$Qid_sacd_suplente = (integer)filter_input(INPUT_POST, 'id_sacd_suplente');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qcl = (bool)filter_input(INPUT_POST, 'cl');
$Qnum_alum = (integer)filter_input(INPUT_POST, 'num_alum');
$Qdedic_ctr_m = (string)filter_input(INPUT_POST, 'dedic_ctr_m');
$Qdedic_ctr_t = (string)filter_input(INPUT_POST, 'dedic_ctr_t');
$Qdedic_ctr_v = (string)filter_input(INPUT_POST, 'dedic_ctr_v');

$QAid_sacd = filter_input(INPUT_POST, 'id_sacd', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAdedic_m = filter_input(INPUT_POST, 'dedic_m', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAdedic_t = filter_input(INPUT_POST, 'dedic_t', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAdedic_v = filter_input(INPUT_POST, 'dedic_v', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/* TODO
 * fase pruebas/real
 */
$oF_ini = new DateTimeLocal();
$oF_fin = new DateTimeLocal();

// Para las funciones
$GesEncargoTipo = new GestorEncargoTipo();

switch ($Qmod) {
    case "nuevo": //nuevo
        if ($Qtipo_centro != "of") { // para el caso de los oficiales no pongo titular ni suplente.
            //Compruebo que estén todos los campos necesasrios
            if (empty($Qid_sacd_titular)) {
                echo _("Debe nombrar un sacerdote tirular") . "<br>";
                exit;
            }
        }
        /* crear encargo: atencion ctr. El tipo de encargo es distinto según el ctr.
           si el id ubi empieza por 1 es sv, si empieza por 2 es sf. */
        $Qid_ubi_txt = (string)$Qid_ubi;
        if ($Qid_ubi_txt[0] == 2) { // sf
            $sf_sv = 2;
            $oCentroSf = new CentroEllas($Qid_ubi);
            $nombre_ubi = $oCentroSf->getNombre_ubi();
            $tipo_ctr = $oCentroSf->getTipo_ctr();
            switch ($tipo_ctr) {
                case "cgioc":
                case "oc":
                    $id_tipo_enc = 2200;
                    break;
                default:
                    $id_tipo_enc = 1200;
            }
        } elseif ($Qid_ubi_txt[0] == 1) { //sv
            $sf_sv = 1;
            $oCentroDl = new CentroDl($Qid_ubi);
            $nombre_ubi = $oCentroDl->getNombre_ubi();
            $tipo_ctr = $oCentroDl->getTipo_ctr();
            switch ($tipo_ctr) {
                case "cgioc":
                case "oc":
                    $id_tipo_enc = 2100;
                    break;
                case "igloc":
                    $id_tipo_enc = 3000;
                    break;
                case "ss":
                    $id_tipo_enc = 1300;
                    break;
                default:
                    $id_tipo_enc = 1100;
            }

        }
        $oEncargoTipo = new EncargoTipo($id_tipo_enc);
        $tipo_enc = $oEncargoTipo->getTipo_enc();
        $desc_enc = $tipo_enc . " ($nombre_ubi)";

        $oEncargo = new Encargo();
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setSf_sv($sf_sv);
        $oEncargo->setId_ubi($Qid_ubi);
        //$oEncargo->setId_zona($id_zona);
        $oEncargo->setDesc_enc($desc_enc);
        //$oEncargo->setIdioma_enc($idioma_enc);
        //$oEncargo->setDesc_lugar($desc_lugar);
        $oEncargo->setObserv($Qobserv);
        if ($oEncargo->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oEncargo->getErrorTxt();
        }

        /* crear horario encargo */
        $id_enc = $oEncargo->getId_enc();
        // horario de mañana (m-matí)
        if (!empty($Qdedic_ctr_m)) {
            $GesEncargoTipo->insert_horario_ctr($id_enc, 'm', $Qdedic_ctr_m, $Qn_sacd);
        }
        // horario de tarde 1ª hora (t-tarda)
        if (!empty($Qdedic_ctr_t)) {
            $GesEncargoTipo->insert_horario_ctr($id_enc, 't', $Qdedic_ctr_t, $Qn_sacd);
        }
        // horario de tarde 2ª hora (v-vespre)
        if (!empty($Qdedic_ctr_v)) {
            $GesEncargoTipo->insert_horario_ctr($id_enc, 'v', $Qdedic_ctr_v, $Qn_sacd);
        }

        /* horarios sacd */
        for ($i = 0; $i < $Qsacd_num; $i++) {
            if ($i > 0) {
                $modo = 5; // colaborador
            } else {
                if ($Qcl) {
                    $modo = 2;
                } else {
                    $modo = 3;
                } // titular de cl - no cl.
                $QAid_sacd[0] = $Qid_sacd_titular;
            }
            $oEncargoSacd = $GesEncargoTipo->insert_sacd($id_enc, $QAid_sacd[$i], $modo);
            $id_item_t_sacd = $oEncargoSacd->getId_item();

            if ($QAdedic_m[$i]) {
                $GesEncargoTipo->insert_horario_sacd($id_item_t_sacd, $id_enc, $QAid_sacd[$i], 'm', $QAdedic_m[$i]);
            }
            if ($QAdedic_t[$i]) {
                $GesEncargoTipo->insert_horario_sacd($id_item_t_sacd, $id_enc, $QAid_sacd[$i], 't', $QAdedic_t[$i]);
            }
            if ($QAdedic_v[$i]) {
                $GesEncargoTipo->insert_horario_sacd($id_item_t_sacd, $id_enc, $QAid_sacd[$i], 'v', $QAdedic_v[$i]);
            }
        }

        if (!empty($Qid_sacd_suplente)) {
            $GesEncargoTipo->insert_sacd($id_enc, $Qid_sacd_suplente, 4);
        }
        // para grabar los datos del número de alumnos (si es un cgi).
        if (strstr($Qtipo_centro, "cgi")) {
            $GesEncargoTipo->grabar_alumnos($Qid_ubi, $Qnum_alum);
        }
        break;
    case "editar": //modificar
        if ($Qtipo_centro != "of") { // para el caso de los oficiales no pongo titular ni suplente.
            //Compruebo que estén todos los campos necesarios
            if (empty($Qid_sacd_titular)) {
                if (!empty($Qid_enc)) { // Si existe el ancargo, lo elimino.
                    $oEncargo = new Encargo(array('id_enc' => $Qid_enc));
                    $oEncargo->DBEliminar();
                    exit;
                } else {
                    echo _("Debe nombrar un sacerdote tirular") . "\n";
                    exit;
                }
            }
            //Compruebo que el titular y suplente sean distintos (excepto para los oficiales de dl)
            if ($Qid_sacd_titular == $Qid_sacd_suplente) {
                exit(_("El sacd titular y suplente deben ser distintos"));
            }

            /* encargo: atencion ctr */
            // Quedaria poder cambiar la zona, el idioma...
            $oEncargo = new Encargo(array('id_enc' => $Qid_enc));
            $oEncargo->DBCarregar();
            $oEncargo->setObserv($Qobserv);
            if ($oEncargo->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oEncargo->getErrorTxt();
            }
            /* modificar horario encargo */
            // horario de mañana (m-matí)
            $GesEncargoTipo->modificar_horario_ctr($Qid_enc, 'm', $Qdedic_ctr_m, $Qn_sacd);
            // horario de tarde 1ª hora (t-tarda)
            $GesEncargoTipo->modificar_horario_ctr($Qid_enc, 't', $Qdedic_ctr_t, $Qn_sacd);
            // horario de tarde 2ª hora (v-vespre)
            $GesEncargoTipo->modificar_horario_ctr($Qid_enc, 'v', $Qdedic_ctr_v, $Qn_sacd);
        }

        /* sacd */
        $GesEncargoSacd = new GestorEncargoSacd();
        $aWhere = array();
        $aOperador = array();
        $aWhere['id_enc'] = $Qid_enc;
        $aWhere['modo'] = '5';
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);
        foreach ($cEncargosSacd as $oEncargoSacd) { // pongo f_fin a todos los sacd del encargo.
            $oEncargoSacd->DBCarregar();
            $oEncargoSacd->setF_fin($oF_fin);
            if ($oEncargoSacd->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oEncargoSacd->getErrorTxt();
            }
            // también a todos los horarios
            $id_nom = $oEncargoSacd->getId_nom();
            $GesEncargoTipo->finalizar_horario_sacd($Qid_enc, $id_nom, $oF_fin);
        }

        for ($i = 0; $i < $Qsacd_num; $i++) {
            if ($i > 0) { // sacd colaboradores
                if (!empty($QAid_sacd[$i])) {
                    $GesEncargoTipo->insert_sacd($Qid_enc, $QAid_sacd[$i], 5);
                }
            } else { // sacd titular
                $GesEncargoSacd = new GestorEncargoSacd();
                $aWhere = array();
                $aOperador = array();
                $aWhere['id_enc'] = $Qid_enc;
                $aWhere['modo'] = '(2|3)';
                $aWhere['f_fin'] = 'x';
                $aOperador['f_fin'] = 'IS NULL';
                $aOperador['modo'] = '~';
                $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);
                $actual_id_sacd_titular = 0;
                foreach ($cEncargosSacd as $oEncargoSacd) { // se supone que sólo hay uno.
                    $actual_id_sacd_titular = $oEncargoSacd->getId_nom();
                    $actual_modo = $oEncargoSacd->getModo();
                }
                if ($Qcl) {
                    $modo = 2;
                } else {
                    $modo = 3;
                }
                if ($actual_id_sacd_titular != $Qid_sacd_titular) {
                    $GesEncargoTipo->insert_sacd($Qid_enc, $Qid_sacd_titular, $modo);
                    $GesEncargoTipo->finalizar_horario_sacd($Qid_enc, $actual_id_sacd_titular, $oF_fin);
                    if (!empty($actual_id_sacd_titular)) {
                        $GesEncargoTipo->finalizar_sacd($Qid_enc, $actual_id_sacd_titular, $actual_modo, $oF_fin);
                    }
                } elseif ($actual_modo != $modo) {
                    // puede ser que ya exista...
                    $GesEncargoSacd2 = new GestorEncargoSacd();
                    $aWhere = [
                        'id_enc' => $Qid_enc,
                        'id_nom' => $Qid_sacd_titular,
                        'modo' => $modo,
                        'f_ini' => $oF_ini->getIso(),
                    ];
                    $cEncargosSacd2 = $GesEncargoSacd2->getEncargosSacd($aWhere);
                    foreach ($cEncargosSacd2 as $oEncargoSacd2) { // aunque sólo debería haber una.
                        if ($oEncargoSacd2->DBEliminar() === false) {
                            echo _("hay un error, no se ha eliminado");
                            echo "\n" . $oEncargoSacd2->getErrorTxt();
                        }
                    }
                    $oEncargoSacd->setModo($modo);
                    if ($oEncargoSacd->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                        echo "\n" . $oEncargoSacd->getErrorTxt();
                    }
                }
                $QAid_sacd[0] = $Qid_sacd_titular;
            }
            if (!empty($QAid_sacd[$i])) { // si está vacio salto.
                // busco el id_item de la tarea_sacd.
                $GesEncargoSacd = new GestorEncargoSacd();
                $aWhere = array();
                $aOperador = array();
                $aWhere['id_nom'] = $QAid_sacd[$i];
                $aWhere['id_enc'] = $Qid_enc;
                $aWhere['modo'] = '(2|3|5)';
                $aWhere['f_fin'] = 'x';
                $aOperador['f_fin'] = 'IS NULL';
                $aOperador['modo'] = '~';
                $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);
                $id_item_t_sacd = 0;
                if (count($cEncargosSacd) > 1) echo _("Error con las tareas");
                foreach ($cEncargosSacd as $oEncargoSacd) { // se supone que sólo hay uno.
                    $id_item_t_sacd = $oEncargoSacd->getId_item();
                }

                $GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd, $Qid_enc, $QAid_sacd[$i], 'm', $QAdedic_m[$i]);
                $GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd, $Qid_enc, $QAid_sacd[$i], 't', $QAdedic_t[$i]);
                $GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd, $Qid_enc, $QAid_sacd[$i], 'v', $QAdedic_v[$i]);
            }
        }

        if (!empty($Qid_sacd_suplente)) {
            $GesEncargoSacd = new GestorEncargoSacd();
            $aWhere = array();
            $aOperador = array();
            $aWhere['id_enc'] = $Qid_enc;
            $aWhere['modo'] = '4';
            $aWhere['f_fin'] = 'x';
            $aOperador['f_fin'] = 'IS NULL';
            $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);
            if (is_array($cEncargosSacd) && count($cEncargosSacd) == 0) {
                $GesEncargoTipo->insert_sacd($Qid_enc, $Qid_sacd_suplente, 4);
            } else {
                foreach ($cEncargosSacd as $oEncargoSacd) { // se supone que sólo hay uno.
                    $actual_id_sacd_suplente = $oEncargoSacd->getId_nom();
                    if ($actual_id_sacd_suplente != $Qid_sacd_suplente) {
                        $oEncargoSacd->setF_fin($oF_fin);
                        if ($oEncargoSacd->DBGuardar() === false) {
                            echo _("hay un error, no se ha guardado");
                            echo "\n" . $oEncargoSacd->getErrorTxt();
                        }
                        $GesEncargoTipo->insert_sacd($Qid_enc, $Qid_sacd_suplente, 4);
                    }
                }
            }
        } else { // eliminar suplente
            $GesEncargoSacd = new GestorEncargoSacd();
            $cEncargosSacd = $GesEncargoSacd->getEncargosSacd(array('id_enc' => $Qid_enc, 'modo' => 4));
            foreach ($cEncargosSacd as $oEncargoSacd) { // aunque sólo debería haber una.
                $oEncargoSacd->DBCarregar();
                $oEncargoSacd->setF_fin($oF_fin);
                if ($oEncargoSacd->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oEncargoSacd->getErrorTxt();
                }
            }
        }
        // para grabar los datos del número de alumnos (si es un cgi).
        if (strstr($Qtipo_centro, "cgi")) {
            //$GesEncargoTipo->grabar_alumnos($Qid_ubi,$Qnum_alum);
        }
        break;
}
