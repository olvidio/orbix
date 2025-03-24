<?php

use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoTipo;
use web\Desplegable;

/**
 * Esta página actualiza la base de datos de los encargos.
 *
 * Se le puede pasar la varaible $mod.
 *    Si es 1 >> inserta un nuevo encargo.
 *    Si es 2 >> sólo cambia el tipo de encargo. Antes utiliza la función comprobar_cambio_tipo($id_activ,$id_tipo_enc)
 * que está en func_tablas.
 *       Si es 3 >> elimina.
 * Existe otra variable: $que. Si vale "actualizar", el $go_to se cambia para volver a ver_ficha_cos.
 *
 * @package    delegacion
 * @subpackage    encargos
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

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qfiltro_ctr = (integer)filter_input(INPUT_POST, 'filtro_ctr');


switch ($Qque) {
    case "lst_tipo_enc":
        $Qid_tipo_enc = (integer)filter_input(INPUT_POST, 'id_tipo_enc');
        $Qgrupo = (integer)filter_input(INPUT_POST, 'grupo');

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tipo_enc'] = '^' . $Qgrupo;
        $aOperador['id_tipo_enc'] = '~';
        $oGesEncargoTipo = new GestorEncargoTipo();
        $cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

        // desplegable de nom_tipo
        $posibles_encargo_tipo = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
        }
        $oDesplNoms = new Desplegable();
        $oDesplNoms->setNombre('id_tipo_enc');
        $oDesplNoms->setOpciones($posibles_encargo_tipo);
        $oDesplNoms->setOpcion_sel($Qid_tipo_enc);
        $oDesplNoms->setBlanco('t');

        echo $oDesplNoms->desplegable();
        break;
    case "nuevo": //nuevo
        $Qsf_sv = empty($Qfiltro_ctr) ? 1 : $Qfiltro_ctr; // sv

        $Qid_ubi = (integer)filter_input(INPUT_POST, 'lst_ctrs');
        $Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
        $Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
        $Qidioma_enc = (string)filter_input(INPUT_POST, 'idioma_enc');
        $Qdesc_lugar = (string)filter_input(INPUT_POST, 'desc_lugar');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        $Qid_tipo_enc = (string)filter_input(INPUT_POST, 'id_tipo_enc');
        $Qgrupo = (string)filter_input(INPUT_POST, 'grupo');
        $Qnom_tipo = (string)filter_input(INPUT_POST, 'nom_tipo');

        if (!empty($Qid_tipo_enc) and !strstr($Qid_tipo_enc, '.')) {
            $id_tipo_enc = $Qid_tipo_enc;
        } else {
            $condta = (new GestorEncargoTipo)->id_tipo_encargo($Qgrupo, $Qnom_tipo);
            if (!strstr($condta, '.')) {
                $id_tipo_enc = $condta;
            } else {
                echo _("Debe seleccionar un tipo de encargo") . "<br>";
                exit;
            }
        }
        // para los encargos personales, no hay sección:
        if ($Qid_tipo_enc[0] == 7) $Qsf_sv = 0;

        //Compruebo que estén todos los campos necesarios
        if (empty($Qdesc_enc)) {
            echo _("Debe llenar el campo descripción") . "<br>";
            exit;
        }

        $oEncargo = new Encargo();
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setSf_sv($Qsf_sv);
        $oEncargo->setId_ubi($Qid_ubi);
        $oEncargo->setId_zona($Qid_zona);
        $oEncargo->setDesc_enc($Qdesc_enc);
        $oEncargo->setIdioma_enc($Qidioma_enc);
        $oEncargo->setDesc_lugar($Qdesc_lugar);
        $oEncargo->setObserv($Qobserv);
        if ($oEncargo->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oEncargo->getErrorTxt();
        }
        $oEncargo->DBCarregar();

        break;
    case "editar": // modificar 
        $Qsf_sv = empty($Qfiltro_ctr) ? 1 : $Qfiltro_ctr;
        $Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');

        $Qid_ubi = (integer)filter_input(INPUT_POST, 'lst_ctrs');
        $Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
        $Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
        $Qidioma_enc = (string)filter_input(INPUT_POST, 'idioma_enc');
        $Qdesc_lugar = (string)filter_input(INPUT_POST, 'desc_lugar');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        $Qid_tipo_enc = (string)filter_input(INPUT_POST, 'id_tipo_enc');
        $Qgrupo = (string)filter_input(INPUT_POST, 'grupo');

        //Compruebo que estén todos los campos necesasrios
        if (empty($Qdesc_enc)) {
            echo _("Debe llenar el campo descripción") . "<br>";
            exit;
        }

        $oEncargo = new Encargo($Qid_enc);
        $oEncargo->DBCarregar();

        $oEncargo->setId_tipo_enc($Qid_tipo_enc);
        $oEncargo->setSf_sv($Qsf_sv);
        $oEncargo->setId_ubi($Qid_ubi);
        $oEncargo->setId_zona($Qid_zona);
        $oEncargo->setDesc_enc($Qdesc_enc);
        $oEncargo->setIdioma_enc($Qidioma_enc);
        $oEncargo->setDesc_lugar($Qdesc_lugar);
        $oEncargo->setObserv($Qobserv);
        if ($oEncargo->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oEncargo->getErrorTxt();
        }

        break;
    case "eliminar":
        if (!empty($_POST['sel'])) {
            $id_enc = strtok($_POST['sel'][0], "#");
            $oEncargo = new Encargo(array('id_enc' => $id_enc));
            if ($oEncargo->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $oEncargo->getErrorTxt();
            }
            // También elimino todos los horarios con sus excepciones (por el postgres: foreginKey)
            /*
            $sql="DELETE FROM t_horario_enc WHERE id_enc=$id_enc";
                $oDBSt_q=$oDB->query($sql);
            $sql="DELETE FROM t_horario_excepcion WHERE id_enc=$id_enc";
                $oDBSt_q=$oDB->query($sql);
            $sql="DELETE FROM t_horario_sacd WHERE id_enc=$id_enc";
                $oDBSt_q=$oDB->query($sql);
            */
        }
        break;
} // fin del switch de mod.