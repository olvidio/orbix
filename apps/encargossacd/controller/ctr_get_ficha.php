<?php

use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoHorario;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoTipo;
use personas\model\entity\GestorPersona;
use ubis\model\entity\CentroDl;
use web\Desplegable;
use web\Hash;

/**
 * Esta página muestra la ficha de atención sacerdotal de un centro. Se inserta en ctr_ficha.php
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

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qseleccion_sacd = (integer)filter_input(INPUT_POST, 'seleccion_sacd');

$f_hoy = date('Y-m-d');

// Para las funciones
$GesEncargoTipo = new GestorEncargoTipo();

list($chk_prelatura, $chk_de_paso, $chk_sssc, $oDesplSacd) = getDesplegableSacdyCheckBox($Qseleccion_sacd);

/* Miro el tipo de ctr. Si es el de oficiales dl, no pongo titular ni suplente. */
$oCentro = new CentroDl($Qid_ubi);
$tipo_centro = $oCentro->getTipo_ctr();

$oEncargoTipo = new EncargoTipo();

/* busco los datos del encargo que se tengan, para los tipos de encargo de atención de centros: 100,1100,1200,1300,2100,2200,3000. */
$aWhere = [];
$aOperador = [];
$GesEncargos = new GestorEncargo();
$aWhere['id_ubi'] = $Qid_ubi;
$aWhere['id_tipo_enc'] = '(1|2|3).0';
$aOperador['id_tipo_enc'] = '~';
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);
//print_r($cEncargos);
$cl_checked = [];
$mod_horario = [];
$a_id_enc = [];
$a_observ = [];
$a_desc_enc = [];
$otros_sacd = [];
$dedic_m = [];
$dedic_t = [];
$dedic_v = [];
if (is_array($cEncargos) && count($cEncargos) == 0) { // nuevo encargo
    $mod = 'nuevo'; // es una ficha nueva
    $Qid_ubi_txt = (string)$Qid_ubi;
    if ($Qid_ubi_txt[0] == 2) { // los de la sf nunca son del cl
        $cl_checked[1] = '';
    } else {
        $cl_checked[1] = 'checked';
    }
    // inicializar valores
    $e = 1;
    $sacd_num[$e] = 1;
    $actual_id_sacd_titular[$e] = '';
    $actual_id_sacd_suplente[$e] = '';
    $mod_horario[$e] = '';
    $a_id_enc[$e] = '';
    $a_observ[$e] = '';
    $a_desc_enc[$e] = '';
    $otros_sacd[$e] = '';
    $dedic_m[$e][0] = ''; // Hay que inicializar aunque no tenga sacd.
    $dedic_t[$e][0] = '';
    $dedic_v[$e][0] = '';
    $dedic_ctr_m[$e] = '';
    $dedic_ctr_t[$e] = '';
    $dedic_ctr_v[$e] = '';
} else {
    $mod = 'editar'; // es una ficha para modificar
    $e = 0;
    $dedic_ctr_m = [];
    $dedic_ctr_t = [];
    $dedic_ctr_v = [];
    foreach ($cEncargos as $oEncargo) {
        $e++;
        $id_tipo_enc[$e] = $oEncargo->getId_tipo_enc();
        $oEncargoTipo->setId_tipo_enc($id_tipo_enc[$e]);
        $oEncargoTipo->DBCarregar();
        $mod_horario[$e] = $oEncargoTipo->getMod_horario();
        $a_id_enc[$e] = $oEncargo->getId_enc();
        $a_observ[$e] = $oEncargo->getObserv();
        $a_desc_enc[$e] = $oEncargo->getDesc_enc();
        // horario del encargo, según el tipo
        $GesEncargoHorario = new GestorEncargoHorario();
        $aWhere = array();
        $aOperador = array();
        $aWhere['id_enc'] = $a_id_enc[$e];
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $aWhere['_ordre'] = 'f_ini DESC';
        $cEncargoHorarios0 = $GesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);

        $aWhere['f_fin'] = "'$f_hoy'";
        $aOperador['f_fin'] = '>';
        $aWhere['_ordre'] = 'f_ini DESC';
        $cEncargoHorarios1 = $GesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);

        $cEncargoHorarios = array_merge($cEncargoHorarios0, $cEncargoHorarios1);
        switch ($mod_horario[$e]) {
            case 3: //por horario.
                $h = 0;
                foreach ($cEncargoHorarios as $oEncargoHorario) {
                    $h++;
                    $mas_menos = $oEncargoHorario->getMas_menos();
                    $dia_ref = $oEncargoHorario->getDia_ref();
                    $dia_inc = $oEncargoHorario->getDia_inc();
                    $dia_num = $oEncargoHorario->getDia_num();
                    $h_ini = $oEncargoHorario->getH_ini();
                    $h_fin = $oEncargoHorario->getH_fin();
                    $n_sacd = $oEncargoHorario->getN_sacd();

                    $texto_horario = $GesEncargoTipo->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
                }
                break;
            case 2: // por módulos.
            default:
                $dedic_ctr_m[$e] = '';
                $dedic_ctr_t[$e] = '';
                $dedic_ctr_v[$e] = '';
                foreach ($cEncargoHorarios as $oEncargoHorario) {
                    $modulo = $oEncargoHorario->getDia_ref();
                    switch ($modulo) {
                        case 'm':
                            $dedic_ctr_m[$e] = $oEncargoHorario->getDia_inc();
                            break;
                        case 't':
                            $dedic_ctr_t[$e] = $oEncargoHorario->getDia_inc();
                            break;
                        case 'v':
                            $dedic_ctr_v[$e] = $oEncargoHorario->getDia_inc();
                            break;
                    }
                }
        }

        // sacd
        $GesEncargoSacd = new GestorEncargoSacd();
        $aWhere = array();
        $aOperador = array();
        $aWhere['id_enc'] = $a_id_enc[$e];
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $aWhere['_ordre'] = 'modo,f_ini DESC';
        $cEncargosSacd0 = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);

        $aWhere['f_fin'] = "'$f_hoy'";
        $aOperador['f_fin'] = '>';
        $aWhere['_ordre'] = 'modo,f_ini DESC';
        $cEncargosSacd1 = $GesEncargoSacd->getEncargosSacd($aWhere, $aOperador);

        $cEncargosSacd = array_merge($cEncargosSacd0, $cEncargosSacd1);

        $s = 0;
        $actual_id_sacd_titular[$e] = '';
        $actual_id_sacd_suplente[$e] = '';
        $dedicacion = '';
        $dedic_m[$e][0] = '';
        $dedic_t[$e][0] = '';
        $dedic_v[$e][0] = ''; // Hay que inicializar aunque no tenga sacd.
        $dedic_sacd[$e][0] = '';
        $cl_checked[$e] = '';
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $modo = $oEncargoSacd->getModo();
            switch ($modo) {
                case 2: // titular del cl
                    $cl_checked[$e] = 'checked';
                case 3: // titular no del cl
                    $actual_id_sacd_titular[$e] = $oEncargoSacd->getId_nom();
                    // horario
                    $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
                    $aWhere = array();
                    $aOperador = array();
                    $aWhere['id_enc'] = $a_id_enc[$e];
                    $aWhere['id_nom'] = $actual_id_sacd_titular[$e];
                    $aWhere['f_fin'] = 'x';
                    $aOperador['f_fin'] = 'IS NULL';
                    $aWhere['_ordre'] = 'f_ini DESC';
                    $cEncargoSacdHorarios = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere, $aOperador);
                    switch ($mod_horario[$e]) {
                        case 3: //por horario.
                            $dedic_sacd[$e][0] = '';
                            $h = 0;
                            foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
                                $h++;
                                $mas_menos = $oEncargoSacdHorario->getMas_menos();
                                $dia_ref = $oEncargoSacdHorario->getDia_ref();
                                $dia_inc = $oEncargoSacdHorario->getDia_inc();
                                $dia_num = $oEncargoSacdHorario->getDia_num();
                                $h_ini = $oEncargoSacdHorario->getH_ini();
                                $h_fin = $oEncargoSacdHorario->getH_fin();
                                $n_sacd = $oEncargoSacdHorario->getN_sacd();

                                $texto_horario = $GesEncargoTipo->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
                                if ($h > 1) $dedic_sacd[$e][0] .= " y ";
                                $dedic_sacd[$e][0] .= $texto_horario;
                            }
                            $dedic_sacd[$e][0] = empty($dedic_sacd[$e][0]) ? _("crear horario") : $dedic_sacd[$e][0];
                            break;
                        case 2: // por módulos.
                        default:
                            foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
                                $modulo = $oEncargoSacdHorario->getDia_ref();
                                switch ($modulo) {
                                    case 'm':
                                        $dedic_m[$e][0] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                    case 't':
                                        $dedic_t[$e][0] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                    case 'v':
                                        $dedic_v[$e][0] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                }
                            }
                    }
                    break;
                case 4: // suplente
                    $actual_id_sacd_suplente[$e] = $oEncargoSacd->getId_nom();
                    break;
                case 5: // colaborador
                    $dedicacion1 = '';
                    $s++;
                    $id_nom = $oEncargoSacd->getId_nom();
                    // horario
                    $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
                    $aWhere = array();
                    $aOperador = array();
                    $aWhere['id_enc'] = $a_id_enc[$e];
                    $aWhere['id_nom'] = $id_nom;
                    $aWhere['f_fin'] = 'x';
                    $aOperador['f_fin'] = 'IS NULL';
                    $aWhere['_ordre'] = 'f_ini DESC';
                    $cEncargoSacdHorarios = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere, $aOperador);
                    switch ($mod_horario[$e]) {
                        case 3: //por horario.
                            $dedic_sacd[$e][$s] = '';
                            $h = 0;
                            foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
                                $h++;
                                $mas_menos = $oEncargoSacdHorario->getMas_menos();
                                $dia_ref = $oEncargoSacdHorario->getDia_ref();
                                $dia_inc = $oEncargoSacdHorario->getDia_inc();
                                $dia_num = $oEncargoSacdHorario->getDia_num();
                                $h_ini = $oEncargoSacdHorario->getH_ini();
                                $h_fin = $oEncargoSacdHorario->getH_fin();
                                $n_sacd = $oEncargoSacdHorario->getN_sacd();

                                $texto_horario = $GesEncargoTipo->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
                                if ($h > 1) $dedic_sacd[$e][$s] .= " y ";
                                $dedic_sacd[$e][$s] .= $texto_horario;
                            }
                            $dedic_sacd[$e][$s] = empty($dedic_sacd[$e][$s]) ? _("crear horario") : $dedic_sacd[$e][$s];
                            $dedicacion1 .= "<td>" . $dedic_sacd[$e][$s] . "</td></tr><tr>";
                            break;
                        case 2: // por módulos.
                        default:
                            $dedic_m[$e][$s] = '';
                            $dedic_t[$e][$s] = '';
                            $dedic_v[$e][$s] = '';
                            foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
                                $modulo = $oEncargoSacdHorario->getDia_ref();
                                switch ($modulo) {
                                    case 'm':
                                        $dedic_m[$e][$s] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                    case 't':
                                        $dedic_t[$e][$s] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                    case 'v':
                                        $dedic_v[$e][$s] = $oEncargoSacdHorario->getDia_inc();
                                        break;
                                }
                            }
                            $dedicacion1 .= "<td><input type=text size=1 name=dedic_m[$s] value=" . $dedic_m[$e][$s] . ">" . _("mañanas");
                            $dedicacion1 .= "</td><td><input type=text size=1 name=dedic_t[$s] value=" . $dedic_t[$e][$s] . ">" . _("tarde 1ª hora");
                            $dedicacion1 .= "</td><td><input type=text size=1 name=dedic_v[$s] value=" . $dedic_v[$e][$s] . ">" . _("tarde 2ª hora") . "</td></tr><tr>";

                    }
                    // Hay que asegurar que el sacd está en el desplegable. Puede ser un sacd de la sssc
                    if (!array_key_exists($id_nom, $oDesplSacd->getOpciones())) {
                        //recalcular las opciones, añadiendo los de la sssc
                        $Qseleccion_sacd = 10;
                        list($chk_prelatura, $chk_de_paso, $chk_sssc, $oDesplSacd) = getDesplegableSacdyCheckBox($Qseleccion_sacd);
                    }
                    $oDesplSacd->setOpcion_sel($id_nom);
                    $dedicacion .= "<tr><td>sacd $s:</td><td colspan=3 class=contenido><select name=id_sacd[$s]>";
                    $dedicacion .= $oDesplSacd->options();
                    $dedicacion .= "</td></tr><tr><td class=etiqueta >" . ucfirst(_("dedicación")) . "</td>";
                    $dedicacion .= $dedicacion1;
                    break;
            }
        }

        if (!empty($s)) {
            $sacd_num[$e] = 1 + $s;
            $otros_sacd[$e] = $dedicacion;
        } else {
            $sacd_num[$e] = 1;
            $otros_sacd[$e] = '';
        }
    }
}
$num_enc = $e;


$a_Hash = [];
$a_despl_titular = [];
$a_despl_suplente = [];
for ($e = 1; $e <= $num_enc; $e++) {
    $oHash = new Hash();
    $aCamposHidden = [
        "e" => $e,
        "mod_$e" => $mod,
        "id_enc_$e" => $a_id_enc[$e],
        "id_ubi_$e" => $Qid_ubi,
        "tipo_centro_$e" => $tipo_centro,
        "mod_horario_$e" => $mod_horario[$e],
    ];
    //$oHash->setUrl($url_actualizar);
    if ($tipo_centro !== 'of') {
        $campos_form = 'dedic_ctr_m!dedic_ctr_t!dedic_ctr_v!dedic_m!dedic_t!dedic_v!id_sacd_suplente!id_sacd_titular!observ';
    } else {
        $campos_form = 'dedic_ctr_m!dedic_ctr_t!dedic_ctr_v!dedic_m!dedic_t!dedic_v!observ';
    }
    $oHash->setCamposForm($campos_form);
    $oHash->setcamposNo('id_sacd!sacd_num!cl!refresh');
    $oHash->setArrayCamposHidden($aCamposHidden);

    $a_Hash[$e] = $oHash;

    $oDesplTitular = clone $oDesplSacd;
    // Hay que asegurar que el sacd está en el desplegable. Puede ser un sacd de la sssc
    if (!empty($id_nom) && !array_key_exists($id_nom, $oDesplTitular->getOpciones())) {
        //recalcular las opciones, añadiendo los de la sssc
        $Qseleccion_sacd = 10;
        list($chk_prelatura, $chk_de_paso, $chk_sssc, $oDesplTitular) = getDesplegableSacdyCheckBox($Qseleccion_sacd);
    }
    $oDesplTitular->setOpcion_sel($actual_id_sacd_titular[$e]);
    $a_despl_titular[$e] = $oDesplTitular;

    $oDesplSuplente = clone $oDesplSacd;
    // Hay que asegurar que el sacd está en el desplegable. Puede ser un sacd de la sssc
    if (!empty($id_nom) && !array_key_exists($id_nom, $oDesplSuplente->getOpciones())) {
        //recalcular las opciones, añadiendo los de la sssc
        $Qseleccion_sacd = 10;
        list($chk_prelatura, $chk_de_paso, $chk_sssc, $oDesplSuplente) = getDesplegableSacdyCheckBox($Qseleccion_sacd);
    }
    $oDesplSuplente->setOpcion_sel($actual_id_sacd_suplente[$e]);
    $a_despl_suplente[$e] = $oDesplSuplente;
}

$fase = "AAA";
$perm_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
    $perm_des = TRUE;
}

$url_ficha = 'apps/encargossacd/controller/ctr_get_ficha.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ficha);
$oHashFicha->setCamposForm('id_ubi!seleccion_sacd');
$h_ficha = $oHashFicha->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'num_enc' => $num_enc,
    'perm_des' => $perm_des,
    'a_Hash' => $a_Hash,
    'tipo_centro' => $tipo_centro,
    'fase' => $fase,
    'mod' => $mod,
    'sacd_num' => $sacd_num,
    'oDesplSacd' => $oDesplSacd,
    'a_despl_titular' => $a_despl_titular,
    'a_despl_suplente' => $a_despl_suplente,
    'cl_checked' => $cl_checked,
    'mod_horario' => $mod_horario,
    'a_id_enc' => $a_id_enc,
    'a_observ' => $a_observ,
    'a_desc_enc' => $a_desc_enc,
    'otros_sacd' => $otros_sacd,
    'dedic_m' => $dedic_m,
    'dedic_t' => $dedic_t,
    'dedic_v' => $dedic_v,
    'dedic_ctr_m' => $dedic_ctr_m,
    'dedic_ctr_t' => $dedic_ctr_t,
    'dedic_ctr_v' => $dedic_ctr_v,
    'url_ficha' => $url_ficha,
    'id_ubi' => $Qid_ubi,
    'h_ficha' => $h_ficha,
    'chk_prelatura' => $chk_prelatura,
    'chk_de_paso' => $chk_de_paso,
    'chk_sssc' => $chk_sssc,
];

$oView = new core\ViewTwig('encargossacd/controller');
$oView->renderizar('ctr_get_ficha.html.twig', $a_campos);

/**
 * @param int $Qseleccion_sacd
 * @return array
 */
function getDesplegableSacdyCheckBox(int $Qseleccion_sacd): array
{
    /* lista sacd posibles */
// selecciono según la variable selecion_sacd ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
    $a_Clases = [];
    $chk_prelatura = '';
    $chk_de_paso = '';
    $chk_sssc = '';
    if (empty($Qseleccion_sacd) || ($Qseleccion_sacd & 2)) {
        $a_Clases[] = array('clase' => 'PersonaN', 'get' => 'getPersonas');
        $a_Clases[] = array('clase' => 'PersonaAgd', 'get' => 'getPersonas');
        $chk_prelatura = 'checked';
    }
    if ($Qseleccion_sacd & 4) {
        $a_Clases[] = array('clase' => 'PersonaEx', 'get' => 'getPersonasEx');
        $chk_de_paso = 'checked';
    }
    if ($Qseleccion_sacd & 8) {
        $a_Clases[] = array('clase' => 'PersonaSSSC', 'get' => 'getPersonas');
        $chk_sssc = 'checked';
    }

    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersona();
    $GesPersonas->setClases($a_Clases);
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    $aOpciones = [];
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $aOpciones[$id_nom] = $apellidos_nombre;
    }

    $oDesplSacd = new Desplegable('', $aOpciones, '', true);
    return array($chk_prelatura, $chk_de_paso, $chk_sssc, $oDesplSacd);
}
