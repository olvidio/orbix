<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividades\model\entity\GestorActividadDl;
use asistentes\model\entity\GestorAsistente;
use personas\model\entity\GestorPersonaS;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\DateTimeLocal;
use web\Lista;
use web\TiposActividades;

/**
 * Esta página lista las personas que asistieron hace x años a
 * una actividad de periodicidad xx años. El próximo curso les corresponde
 * Servirá para: crt-cel y crt-cv-cel (para los cel), crt s interno (para todos los s)
 * También se usa para el seguimiento de asistencias durante el curso de cv-s anuales
 * y de crt
 *
 *
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Josep Companys
 * @since        15/08/03
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qcurso = (string)filter_input(INPUT_POST, 'curso'); // actual, anterior
$Qid_ubi = (string)filter_input(INPUT_POST, 'id_ubi');

$any = date("Y");

// Centros
$aWhereP = [];
$aOperadorP = [];
if (!empty($Qid_ubi) && ($Qid_ubi != 999)) {
    $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $oCentroDl = $CentroDlRepository->findById($Qid_ubi);
    $nombre_ubi = $oCentroDl->getNombre_ubi();
    $aWhereP['id_ctr'] = $Qid_ubi;
    $aWhereP['_ordre'] = 'apellido1, apellido2, nom';
} else { //todos los crt
    $nombre_ubi = '';
}

//// FECHAS
/* 22.09.21 se cambia (antes era por curso estudios)
 * 
 * - vsg/crt/s que han de fer l'intern: haurien de sortir els s (no cel) que fa més de 3 anys que no fan un intern.
 * - vsg/crt/s que han de fer el crt de cel: haurien de sortir els s cel que fa més de tres anys que no han fet un intern (no cal que sigui el crt de cel).
 * - vsg/cv s/s que no han anat a la cv/aquest curs: hauria de ser després del 1 de gener de l'any actual.
 * - vsg/cv s/s que no han anat a la cv/el curs passat:hauria de ser entre 1 de gener de l'any passat i el 31 de desembre de l'any passat
 * 
 * 11.11.21 volver a cambiar fechas:
 * -  s no celadores que NO han asistido al crt interno entre 1-07-2018 i 30-06-2021 (búsqueda en julio 2021)
 * -  s celadores que NO han asistido a un crt interno entre  1-07-2018 i 30-06-2021 (búsqueda en julio 2021)
 * -  s que no han fet el crt:
 *          Aquest any: hauria de mirar del 1-07-2021 fins a dia d'avui (o fins 30-06-2022)
 *          El curs passat: hauria de mirar del 1-07-2020 al 30-06-2021
 * 
 */
$aWhereA = [];
$aOperadorA = [];

// ctr:
if (strstr($Qque, 'crt')) {
    $mes_ini = $_SESSION['oConfig']->getMesIniCrt();
    $dia_ini = $_SESSION['oConfig']->getDiaIniCrt();
    $mes_fin = $_SESSION['oConfig']->getMesFinCrt();
    $dia_fin = $_SESSION['oConfig']->getDiaFinCrt();
    $any = $_SESSION['oConfig']->any_final_curs('crt');
} else {
    $mes_ini = $_SESSION['oConfig']->getMesIniStgr();
    $dia_ini = $_SESSION['oConfig']->getDiaIniStgr();
    $mes_fin = $_SESSION['oConfig']->getMesFinStgr();
    $dia_fin = $_SESSION['oConfig']->getDiaFinStgr();
    $any = $_SESSION['oConfig']->any_final_curs('est');
}
switch ($Qcurso) {
    case 'anterior':
        $any_ini = $any - 2;
        $any_fin = $any - 1;
        break;
    case 'actual':
        $any_ini = $any - 1;
        $any_fin = $any;
        break;
    default:
        // para los cel (3 años):
        if ($Qque === "crt_cel" || $Qque === "crt_s") {
            $any_ini = $any - 4;
            $any_fin = $any - 1;
        } else {
            $any_ini = $any - 1;
            $any_fin = $any;
        }
}
$oDateIni = new DateTimeLocal("$any_ini/$mes_ini/$dia_ini");
$oDateFin = new DateTimeLocal("$any_fin/$mes_fin/$dia_fin");

$QempiezaminIso = $oDateIni->getIso();
$QfinIso = $oDateFin->getIso();
$fecha_ini = $oDateIni->getFromLocal('-');
$fecha_fin = $oDateFin->getFromLocal('-');
$titulo_fecha = sprintf(_("entre %s y %s"), $fecha_ini, $fecha_fin);

//// Tipo Actividad
$alert = '';
switch ($Qque) {
    case "crt_s_sg":
        $aWhereA['id_tipo_activ'] = '^1[45]1';
        $aOperadorA['id_tipo_activ'] = '~';
        if (empty($nombre_ubi)) {
            $titulo_actividad = _("s que todavía no han asistido a crt-s o crt-sg");
        } else {
            $titulo_actividad = sprintf(_("s de %s que todavía no han asistido a crt-s o crt-sg"), $nombre_ubi);
        }
        break;
    case "crt_s":
        $aWhereA['id_tipo_activ'] = '^141';
        $aOperadorA['id_tipo_activ'] = '~';
        $titulo_actividad = sprintf(_("s no celadores que NO han asistido al crt interno"));
        $alert = '*' . sprintf(_("para indicar si es celador, el campo Eap debe contener algo tipo: 'C12'"));
        $aWhereP['eap'] = "COALESCE(eap,'x') !~* 'C\d\d'";
        $aOperadorP['eap'] = 'TXT';
        break;
    case "crt_cel":
        $aWhereA['id_tipo_activ'] = '^141';
        $aOperadorA['id_tipo_activ'] = '~';
        $titulo_actividad = sprintf(_("s celadores que NO han asistido a un crt interno"));
        $alert = '*' . sprintf(_("para indicar si es celador, el campo Eap debe contener algo tipo: 'C12'"));
        $aWhereP['eap'] = "COALESCE(eap,'x') ~* 'C\d\d'";
        $aOperadorP['eap'] = 'TXT';
        break;
    case "cv_s":
        $aWhereA['id_tipo_activ'] = '^143';
        $aOperadorA['id_tipo_activ'] = '~';
        if (empty($nombre_ubi)) {
            $titulo_actividad = _("s que no han asistido a cv de s");
        } else {
            $titulo_actividad = sprintf(_("s de %s que todavía no han asistido a cv de s"), $nombre_ubi);
        }
        break;
    case "cv_s_ad":
        $aWhereA['id_tipo_activ'] = '1431';
        $titulo_actividad = sprintf(_("s con ad reciente -entre 6 y 18 meses antes de la fecha última cv admisión del año- que todavía no han asistido a cv de ad"));
        // fecha última actividad:
        // final de curso:
        $any = date('Y');
        $fin_d = $_SESSION['oConfig']->getDiaFinCrt();
        $fin_m = $_SESSION['oConfig']->getMesFinCrt();
        $f_iso_final = "$any-$fin_m-$fin_d";

        $aWhereUltima = ['id_tipo_activ' => '1431',
                'status' => 2,
                'f_ini' => $f_iso_final,
                '_ordre' => 'f_ini DESC',
        ];
        $aOperadorUltima = ['f_ini' => '<'];
        $gesActividades = new GestorActividadDl();
        $cActividades = $gesActividades->getActividades($aWhereUltima, $aOperadorUltima);
        if (is_array($cActividades) && !empty($cActividades)) {
            $oActividadU = $cActividades[0];
            $oFini = $oActividadU->getF_fin();
            $nom_activ = $oActividadU->getNom_activ();
        } else {
            // fin de año
            $oFini = new DateTimeLocal();
            $nom_activ = _("No hay");
        }
        // 6 meses antes:
        $iso_fin = $oFini->sub(new DateInterval('P6M'))->getIso();
        // 18 meses antes:
        $iso_ini = $oFini->sub(new DateInterval('P12M'))->getIso();
        //AND p.f_ad BETWEEN '$f_ad_min' AND '$f_ad_max'
        $aWhereP['inc'] = 'ad';
        $aWhereP['f_inc'] = "'$iso_ini','$iso_fin'";
        $aOperadorP['f_inc'] = 'BETWEEN';
        $alert = '*' . sprintf(_("última cv: %s"), $nom_activ);
        break;
    case "cv_jovenes":
        $titulo_actividad = _("s jóvenes (<30) que no han asistido a cv de s");
        $f_joven = date('Y-m-d', mktime(0, 0, 0, 1, 1, $any - 30));
        //AND f_nacimiento > '$f_joven' AND ini_ce IS NULL AND fin_ce IS NULL
        $aWhereP['f_nacimiento'] = $f_joven;
        $aOperadorP['f_nacimiento'] = '>';
        $aWhereP['ce_ini'] = 'x';
        $aOperadorP['ce_ini'] = 'IS NULL';
        $aWhereP['ce_fin'] = 'x';
        $aOperadorP['ce_fin'] = 'IS NULL';
        break;
        break;
    default:
        exit (_("No sé en que tipo de actividad hay que mirar las asistencias"));
}
// Id de actividades posibles
$aWhereA['_ordre'] = 'f_ini DESC';
$GesActividades = new GestorActividad();
$a_id_activ_f_ini = $GesActividades->getArrayIdsWithKeyFini($aWhereA, $aOperadorA);

$titulo = $titulo_actividad . ' ' . $titulo_fecha;

$aWhereP['situacion'] = 'A';
$GesPersonasS = new GestorPersonaS();
$cPersonas = $GesPersonasS->getPersonasDl($aWhereP, $aOperadorP);
$i = 0;
$falta = 0;
$a_valores = [];
$gesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
$GesAsistente = new GestorAsistente();
foreach ($cPersonas as $oPersona) {
    $i++;
    $id_nom = $oPersona->getId_nom();
    $ape_nom = $oPersona->getPrefApellidosNombre();

    //Buscar el ctr (si no está en la selección)
    if ($Qid_ubi == 999 || empty($Qid_ubi)) {
        $nombre_ubi = '';
        $id_ctr = $oPersona->getId_ctr();
        $cCentros = $gesCentros->getCentros(['id_ubi' => $id_ctr]);
        if (is_array($cCentros) && !empty($cCentros)) {
            $nombre_ubi = $cCentros[0]->getNombre_ubi();
        }
    }

    $cAsistentes = $GesAsistente->getAsistenciasPersonaDeActividades($id_nom, $a_id_activ_f_ini, true);
    if (!empty($cAsistentes)) {
        reset($cAsistentes);
        $oAsistente = current($cAsistentes);
        $id_activ = $oAsistente->getId_activ();
        $oActividad = new ActividadAll($id_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oFini = $oActividad->getF_ini();
        $f_ini_iso = $oFini->getIso();

        if ($f_ini_iso >= $QempiezaminIso && $f_ini_iso <= $QfinIso) {
            continue;
        }
        $f_ini = $oFini->getFromLocal();

        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $sactividad = $oTipoActividad->getActividadText();
        $sasistentes = $oTipoActividad->getAsistentesText();
        $snom_tipo = $oTipoActividad->getNom_tipoText();
        $msg = "$sactividad  $sasistentes  $snom_tipo";
    } else {
        $msg = _("No hay datos");
        $f_ini = _("No consta");
        $a_valores[$i]['clase'] = 'tono2';
    }
    $falta++;

    $a_valores[$i][1] = $ape_nom;
    $a_valores[$i][2] = $nombre_ubi;
    $a_valores[$i][3] = $f_ini;
    $a_valores[$i][4] = $msg;
}

$a_cabeceras = [ucfirst(_("apellidos, nombre")),
        ucfirst(_("centro")),
        ucfirst(_("fecha última asistencia")),
        ucfirst(_("tipo actividad")),
];

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);
//-------------------------------------- html -------------------------------------------------
?>
<?= $alert ?>
    <h3><?= $titulo ?></h3>
<?php
printf(_('número de personas: %s de %s'), $falta, $i);
echo $oTabla->lista();
