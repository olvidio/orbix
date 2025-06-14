<?php

use actividades\model\entity\ActividadAll;
use actividadestudios\model\entity\GestorMatricula;
use asignaturas\model\entity\Asignatura;
use asistentes\model\entity\GestorAsistente;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\Persona;
use ubis\model\entity\GestorCentroDl;
use web\Lista;
use web\Periodo;
use function core\is_true;

/**
 * Listado del plan de estudios por ctr
 *
 *
 *
 *
 * @package    delegacion
 * @subpackage    personas
 * @author    Daniel Serrabou
 * @since        3/6/03.
 *
 */


// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

// sólo las actividades de estudios:

//generamos el periodo de la búsqueda de actividades
//en función de las condiciones que tengamos:
$oHoy = new web\DateTimeLocal();

$Qn_agd = (string)filter_input(INPUT_POST, 'n_agd');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');


// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$aGoBack = array(
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
);
$oPosicion->setParametros($aGoBack, 1);

$aWhereNom['propio'] = 't';
$aWhereAct['f_ini'] = "'$inicioIso','$finIso'";
$aOperadorAct['f_ini'] = 'BETWEEN';
$aWhereAct['id_tipo_activ'] = "^1(12|33)";
$aOperadorAct['id_tipo_activ'] = "~";

$aWhereCtr = [];
$aOperadorCtr = [];
switch ($Qn_agd) {
    case "a":
        $tabla = "p_agregados";
        $aWhereCtr['tipo_ctr'] = '^a';
        $aOperadorCtr['tipo_ctr'] = '~';
        break;
    case "n":
        $tabla = "p_numerarios";
        $aWhereCtr['tipo_ctr'] = '^n';
        $aOperadorCtr['tipo_ctr'] = '~';
        break;
    case "nm":
        $tabla = "p_n_agd";
        $aWhereCtr['tipo_ctr'] = '^nm';
        $aOperadorCtr['tipo_ctr'] = '~';
        break;
    case "nj":
        $tabla = "p_n_agd";
        $aWhereCtr['tipo_ctr'] = '^nj(ce)*';
        $aOperadorCtr['tipo_ctr'] = '~';
        break;
    case "sssc":
        $tabla = "p_n_agd";
        $aWhereCtr['tipo_ctr'] = '^ss';
        $aOperadorCtr['tipo_ctr'] = '=';
        break;
    case "c":
        $tabla = "p_n_agd";
        $aWhereCtr['id_ubi'] = $Qid_ubi;
        $aOperadorCtr = [];
        break;
    default:
        $tabla = "p_n_agd";
}

// primero selecciono los centros
$GesCentrosDl = new GestorCentroDl();
$cCentros = $GesCentrosDl->getCentros($aWhereCtr, $aOperadorCtr);
$a_valores = [];
foreach ($cCentros as $oCentroDl) {
    $id_ubi = $oCentroDl->getId_ubi();
    $aGrupos[$id_ubi] = $oCentroDl->getNombre_ubi();

    $aWhere = [];
    $aOperador = [];
    $aWhere['situacion'] = 'A';
    $aWhere['id_ctr'] = $id_ubi;
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    // Ahora (28.IV.2010) los agd quieren que salgan los sacd y los que no hacen estudios.
    $tipo_ctr = $oCentroDl->getTipo_ctr();
    if (strpos($tipo_ctr, "n") === 0) { // ctr de numerarios.
        $aWhere['sacd'] = 'f';
        $aWhere['stgr'] = 'n';
        $aOperador['stgr'] = '!=';
    }

    $GesPersonas = new GestorPersonaDl();
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
    $i = 0;
    foreach ($cPersonas as $oPersonaDl) {
        $i++;
        $id_nom = $oPersonaDl->getId_nom();
        $nom = $oPersonaDl->getPrefApellidosNombre();
        $stgr = $oPersonaDl->getStgr();
        $a_valores[$id_ubi][$i][1] = $i;
        $a_valores[$id_ubi][$i][2] = $nom;

        //consulta de las actividades de cada persona
        //$condicion = "propio='t' AND $periodo AND id_tipo_activ::text ~ '^1(12|33)'";
        $aWhereNom['id_nom'] = $id_nom;
        $aOperadorNom = [];
        $GesAsistente = new GestorAsistente();
        $cAsistentes = $GesAsistente->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhereAct, $aOperadorAct);
        $a = 0;
        foreach ($cAsistentes as $oAsistente) {
            $a++;
            $id_activ = $oAsistente->getId_activ();
            $oActividad = new ActividadAll($id_activ);
            $nom_activ = $oActividad->getNom_activ();

            $oF_ini = $oActividad->getF_ini();
            // los que ya lo han hecho:
            if ($oF_ini < $oHoy) {
                $nom_activ = _("ya lo ha hecho");
                $asignaturas = '';
            } else {
                switch ($stgr) {
                    case 'n':
                        $asignaturas = _("plan de formación");
                        break;
                    case 'r':
                        $asignaturas = _("repaso");
                        break;
                    default:
                        $asignaturas = '';
                        $GesMatriculas = new GestorMatricula();
                        $cMatriculas = $GesMatriculas->getMatriculas(array('id_nom' => $id_nom, 'id_activ' => $id_activ));
                        foreach ($cMatriculas as $oMatricula) {
                            $id_asignatura = $oMatricula->getId_asignatura();
                            $preceptor = $oMatricula->getPreceptor();
                            $id_preceptor = $oMatricula->getId_preceptor();
                            $oAsignatura = new Asignatura($id_asignatura);
                            $nombre_corto = $oAsignatura->getNombre_corto();
                            $creditos = $oAsignatura->getCreditos();
                            if (is_true($preceptor)) {
                                if (!empty($id_preceptor)) {
                                    $oPersona = Persona::NewPersona($id_preceptor);
                                    // Comprobar si el preceptor asiste al ca.
                                    $aWherePreceptor = ['id_activ' => $id_activ, 'id_nom' => $id_nom];
                                    $cAsistentesP = $GesAsistente->getAsistentes($aWherePreceptor);
                                    if (count($cAsistentesP) > 0) {
                                        $p = '*';
                                    } else {
                                        $p = '';
                                    }
                                    $preceptor = '(p: ' . $oPersona->getPrefApellidosNombre() . ")$p";
                                } else {
                                    $preceptor = '(p)';
                                }
                            } else {
                                $preceptor = '';
                            }
                            $asignaturas .= "$nombre_corto ($creditos " . _("créditos") . ")$preceptor<br>";
                        }
                }
            }
            $a_valores[$id_ubi][$i][3] = $nom_activ;
            $a_valores[$id_ubi][$i][4] = $asignaturas;
        }
    }
}

$a_cabeceras[] = _("nº");
$a_cabeceras[] = _("nombre");
$a_cabeceras[] = _("actividad");
$a_cabeceras[] = _("asignaturas");

asort($aGrupos);

$oLista = new Lista();
$oLista->setGrupos($aGrupos);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setPie(_("(*) El preceptor no asiste al ca"));

echo $oPosicion->mostrar_left_slide(1);
echo $oLista->listaPaginada();