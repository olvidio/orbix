<?php

use core\ConfigGlobal;
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoSacdObserv;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\PersonaDl;
use ubis\model\entity\CentroDl;
use ubis\model\entity\CentroEllas;
use web\DateTimeLocal;
use encargossacd\model\entity\GestorPropuestaEncargosSacd;
use encargossacd\model\entity\GestorPropuestaEncargoSacdHorario;

/**
 * Esta página muestra los encargos de un sacd.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsel = (string)filter_input(INPUT_POST, 'sel');

/* claves:
 *       "com_sacd";
 *       "t_titular"
 *       "t_secc"
 *       "t_mañanas"
 *       "t_tarde1"
 *       "t_tarde2"
 *       "t_suplente"
 *       "t_colaborador"
 *       "t_otros"
 *       "t_observ"
 */

// para ordenar los modos: 'modo'=>orden
$hoy_iso = date('Y-m-d');

$array_orden = array('1' => 1, '2' => 2, '3' => 2, '4' => 4, '5' => 3, '6' => 5);
// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();

// los sacd
$GesPersonas = new GestorPersonaDl();
$aWhere = [];
$aOperador = [];
switch ($Qsel) {
    case "nagd":
        $aWhere['id_tabla'] = '^n|^a';
        $aWhere['situacion'] = 'A';
        $aWhere['sacd'] = 't';
        $aWhere['dl'] = ConfigGlobal::mi_delef();
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $aOperador['id_tabla'] = '~';
        $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
        break;
    case "sssc":
        $aWhere['id_tabla'] = '^sss';
        $aWhere['situacion'] = 'A';
        $aWhere['sacd'] = 't';
        $aWhere['dl'] = ConfigGlobal::mi_delef();
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $aOperador['id_tabla'] = '~';
        $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
        break;
}
$array_modo = array();
$s = 0;
foreach ($cPersonas as $oPersona) {
    $s++;
    $id_nom = $oPersona->getId_nom();
    $array_modo[$s]['nom_ap'] = $oPersona->getNombreApellidos();
    $idioma = $oPersona->getLengua();

    $array_modo[$s]['txt']['t_secc'] = $oEncargoFunciones->getTraduccion('t_secc', $idioma);
    $array_modo[$s]['txt']['t_mañanas'] = $oEncargoFunciones->getTraduccion('t_mañanas', $idioma);
    $array_modo[$s]['txt']['t_tardes1'] = $oEncargoFunciones->getTraduccion('t_tardes1', $idioma);
    $array_modo[$s]['txt']['t_tardes2'] = $oEncargoFunciones->getTraduccion('t_tardes2', $idioma);
    $array_modo[$s]['txt']['t_titular'] = $oEncargoFunciones->getTraduccion('t_titular', $idioma);
    $array_modo[$s]['txt']['t_suplente'] = $oEncargoFunciones->getTraduccion('t_suplente', $idioma);
    $array_modo[$s]['txt']['t_colaborador'] = $oEncargoFunciones->getTraduccion('t_colaborador', $idioma);
    $array_modo[$s]['txt']['t_otros'] = $oEncargoFunciones->getTraduccion('t_otros', $idioma);

    // busco las observaciones (si las hay)
    $GesEncargoSacdObserv = new GestorEncargoSacdObserv();
    $cEncargoSacdObserv = $GesEncargoSacdObserv->getEncargoSacdObservs(array('id_nom' => $id_nom));
    if (is_array($cEncargoSacdObserv) && count($cEncargoSacdObserv) > 0) {
        $observ = $cEncargoSacdObserv[0]->getObserv();
    } else {
        $observ = '';
    }
    /* busco los datos del encargo que se tengan */
    $GesEncargoSad = new GestorPropuestaEncargosSacd();
    $aWhere = ['id_nom_new' => $id_nom, 'f_fin' => 'x', '_ordre' => 'modo'];
    $aOperador = ['f_fin' => 'IS NULL'];
    $cEncargosSacd1 = $GesEncargoSad->getEncargosSacd($aWhere, $aOperador);

    $aWhere = ['id_nom_new' => $id_nom, 'f_fin' => $hoy_iso, '_ordre' => 'modo'];
    $aOperador = ['f_fin' => '>'];
    $cEncargosSacd2 = $GesEncargoSad->getEncargosSacd($aWhere, $aOperador);

    $cEncargosSacd = $cEncargosSacd1 + $cEncargosSacd2;
    foreach ($cEncargosSacd as $oEncargoSacd) {
        $id_enc = $oEncargoSacd->getId_enc();
        $modo = $oEncargoSacd->getModo();
        $oEncargo = new Encargo($id_enc);
        $id_tipo_enc = $oEncargo->getId_tipo_enc();
        // paso a texto para poder coger el segundo caracter.
        if (empty($id_tipo_enc)) continue;
        $id_tipo_enc_txt = (string)$id_tipo_enc;
        if ($id_tipo_enc_txt[0] == 4 || $id_tipo_enc_txt[0] == 7 || $id_tipo_enc_txt[0] == 8) continue;
        $sup_tit = "";
        $desc_enc = $oEncargo->getDesc_enc();
        $id_ubi = $oEncargo->getId_ubi();
        $grupo = $array_orden[$modo];
        if (!empty($id_ubi)) { // en algunos encargos no hay ubi
            //$oUbi = new Centro($id_ubi);
            if (substr($id_ubi, 0, 1) == 2) {
                $oUbi = new CentroEllas($id_ubi);
            } else {
                $oUbi = new CentroDl($id_ubi);
            }
            $nombre_ubi = $oUbi->getNombre_ubi();
        } else {
            $nombre_ubi = "";
        }
        $seccion = '';
        if (!empty($id_tipo_enc)) {
            if ($id_tipo_enc_txt[1] == 2) {
                $seccion = "sf";
            } else {
                $seccion = "sv";
            }
        }

        if ($modo == 2 || $modo == 3) {
            // busco el suplente
            $cEncargosSacd1 = $GesEncargoSad->getEncargosSacd(array('id_enc' => $id_enc, 'f_fin' => 'x', 'modo' => 4), array('f_fin' => 'IS NULL'));
            if (is_array($cEncargosSacd1) && count($cEncargosSacd1) == 0) {
                $cEncargosSacd1 = $GesEncargoSad->getEncargosSacd(array('id_enc' => $id_enc, 'f_fin' => $hoy_iso, 'modo' => 4), array('f_fin' => '>'));
            }
            if (is_array($cEncargosSacd1) && count($cEncargosSacd1) == 1) {
                $id_nom_sup = $cEncargosSacd1[0]->getId_nom_new();
                $oSacd = new PersonaDl($id_nom_sup);
                $sup_tit = $oSacd->getNombreApellidos();
            } else {
                $sup_tit = '';
            }
        } elseif ($modo == 4) {
            // busco el titular
            // busco el suplente
            $cEncargosSacd1 = $GesEncargoSad->getEncargosSacd(array('id_enc' => $id_enc, 'f_fin' => 'x', 'modo' => '[23]'), array('modo' => '~', 'f_fin' => 'IS NULL'));
            if (is_array($cEncargosSacd1) && count($cEncargosSacd1) == 0) {
                $cEncargosSacd1 = $GesEncargoSad->getEncargosSacd(array('id_enc' => $id_enc, 'f_fin' => $hoy_iso, 'modo' => '[23]'), array('modo' => '~', 'f_fin' => '>'));
            }
            if (is_array($cEncargosSacd1) && count($cEncargosSacd1) == 1) {
                $id_nom_tit = $cEncargosSacd1[0]->getId_nom_new();
                $oSacd = new PersonaDl($id_nom_tit);
                $sup_tit = $oSacd->getNombreApellidos();
            } else {
                $sup_tit = '';
            }
        }

        // horario
        $aWhere = array();
        $aOperador = array();
        $GesHorario = new GestorPropuestaEncargoSacdHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = "'$hoy_iso'";
        $aOperador['f_fin'] = '>';

        $cHorarios1 = $GesHorario->getEncargoSacdHorarios($aWhere, $aOperador);
        $aOperador['f_fin'] = 'IS NULL';
        $cHorarios2 = $GesHorario->getEncargoSacdHorarios($aWhere, $aOperador);
        $cHorarios = $cHorarios1 + $cHorarios2;

        $dedic_m = "";
        $dedic_t = "";
        $dedic_v = "";
        foreach ($cHorarios as $oEncargoSacdHorario) {
            $modulo = $oEncargoSacdHorario->getDia_ref();
            switch ($modulo) {
                case "m":
                    $dedic_m = $oEncargoSacdHorario->getDia_inc();
                    break;
                case "t":
                    $dedic_t = $oEncargoSacdHorario->getDia_inc();
                    break;
                case "v":
                    $dedic_v = $oEncargoSacdHorario->getDia_inc();
                    break;
            }
        }

        // estudio, descanso y otros como grupo 6
        if ($id_tipo_enc == 5020 || $id_tipo_enc == 5030 || $id_tipo_enc == 6000) {
            $grupo = 6;
            $nombre_ubi = $oEncargoFunciones->getTraduccion('e_' . $desc_enc, $idioma);
            $dedic_m = $oEncargoFunciones->dedicacion($id_nom, $id_enc, $idioma);
        }

        // las colatios y rtm los pongo al final
        if ($id_tipo_enc == 4002 || $id_tipo_enc == 1110 || $id_tipo_enc == 1210) {
            $otros_enc .= $desc_enc;
            continue;
        }
        if (!empty($id_enc)) {
            $array_enc = array("desc_enc" => $desc_enc,
                "nombre_ubi" => $nombre_ubi,
                "seccion" => $seccion,
                "dedic_m" => $dedic_m,
                "dedic_t" => $dedic_t,
                "dedic_v" => $dedic_v,
                "sup_tit" => $sup_tit
            );
            $array_modo[$s]['grupo'][$grupo][] = $array_enc;
        }
    }
    if (!empty($observ)) {
        $array_modo[$s][7][] = array("desc_enc" => $observ);
    }
} // fin del while de los sacd

$a_campos = ['oPosicion' => $oPosicion,
    'array_modo' => $array_modo,
    'Qsel' => $Qsel,
];

$oView = new core\View('encargossacd/controller');
$oView->renderizar('propuestas_lista_sacd.phtml', $a_campos);
