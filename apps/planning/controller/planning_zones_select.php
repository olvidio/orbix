<?php

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use core\ConfigGlobal;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use personas\model\entity\PersonaSacd;
use planning\domain\Planning;
use planning\domain\PlanningStyle;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use zonassacd\model\entity\Zona;
use function core\is_true;

/**
 * Esta página tiene la misión de realizar la llamada a calendario php;
 * Se ordenan los nombres de los sacd por orden alfabético (sort array).
 * Si hay más de una zona están ordenadas por el campo 'orden' de la zona.
 *
 * @param integer $any El año al que hace referencia.
 * @param integer $trimestre diversas combinaciones de meses.
 * @param string $actividad Si se quiere mostrar las actividades o no (sólo la cuadrícula).
 * @param integer|string $id_zona Puede ser:
 * id_zona, para una zona.
 * 'todo' para todas las zonas.
 * 'todo_propias' para las zonas que tienen asignado a algún sacd como propia.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

// valores del id_cargo de tipo_cargo = sacd:
$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');

$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qtrimestre = (integer)filter_input(INPUT_POST, 'trimestre');

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');


$aGoBack = array(
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'trimestre' => $Qtrimestre,
    'id_zona' => $Qid_zona,
    'actividad' => $Qactividad,
    'propuesta' => $Qpropuesta,
);
$oPosicion->setParametros($aGoBack, 1);


$year = empty($Qyear) ? (integer)date('Y') + 1 : $Qyear;
// ISO: mes/dia
switch ($Qtrimestre) {
    case 1:
        $ini_trim = "1/1";
        $fin_trim = "3/31";
        break;
    case 2:
        $ini_trim = "4/1";
        $fin_trim = "6/30";
        break;
    case 3:
        $ini_trim = "7/1";
        $fin_trim = "9/30";
        break;
    case 4:
        $ini_trim = "10/1";
        $fin_trim = "12/31";
        break;
    case 5:
        $ini_trim = "12/1";
        $fin_trim = "1/31";
        break;
    case 6:
        $ini_trim = "7/1";
        $fin_trim = "8/31";
        break;
    case 101:
        $ini_trim = "1/1";
        $fin_trim = "1/31";
        break;
    case 102:
        $ini_trim = "2/1";
        $fin_trim = "3/1";
        break;
    case 103:
        $ini_trim = "3/1";
        $fin_trim = "3/31";
        break;
    case 104:
        $ini_trim = "4/1";
        $fin_trim = "4/30";
        break;
    case 105:
        $ini_trim = "5/1";
        $fin_trim = "5/31";
        break;
    case 106:
        $ini_trim = "6/1";
        $fin_trim = "6/30";
        break;
    case 107:
        $ini_trim = "7/1";
        $fin_trim = "7/31";
        break;
    case 108:
        $ini_trim = "8/1";
        $fin_trim = "8/31";
        break;
    case 109:
        $ini_trim = "9/1";
        $fin_trim = "9/30";
        break;
    case 110:
        $ini_trim = "10/1";
        $fin_trim = "10/31";
        break;
    case 111:
        $ini_trim = "11/1";
        $fin_trim = "11/30";
        break;
    case 112:
        $ini_trim = "12/1";
        $fin_trim = "12/31";
        break;
}

$inicio_iso = $year . "/" . $ini_trim;
if ($Qtrimestre === 5) {
    $fin_iso = ($year + 1) . "/" . $fin_trim;
} else {
    $fin_iso = $year . "/" . $fin_trim;
}

$oIniPlanning = web\DateTimeLocal::createFromFormat('Y/m/d', $inicio_iso);
$oFinPlanning = web\DateTimeLocal::createFromFormat('Y/m/d', $fin_iso);
$inicio_local = $oIniPlanning->getFromLocal();

switch ($Qid_zona) {
    case "todo_propias":
        $aWhere = [];
        $aWhere['propia'] = 't';
        $GesZonasSacd = new GestorZonaSacd();
        $cZonasSacd = $GesZonasSacd->getZonasSacds($aWhere);
        $a_zonas = [];
        $a_zonas_o = [];
        foreach ($cZonasSacd as $oZonaSacd) {
            $id_zona = $oZonaSacd->getId_zona();
            if (array_key_exists($id_zona, $a_zonas)) {
                continue;
            }
            $oZona = new Zona($id_zona);
            $nombre_zona = $oZona->getNombre_zona();
            $orden = $oZona->getOrden();
            $a_zonas[$id_zona] = $nombre_zona;
            $a_zonas_o[$id_zona] = $orden;
        }
        asort($a_zonas_o);
        $aa_zonas = [];
        foreach ($a_zonas_o as $id_zona => $orden) {
            $aa_zonas[] = array('id_zona' => $id_zona, 'nombre_zona' => $a_zonas[$id_zona]);
        }
        break;
    case "todo":
        $GesZonas = new GestorZona();
        $aOpciones = $oGestorZona->getArrayZonas($id_nom_jefe);
        $oDesplZonas = new Desplegable();
        $oDesplZonas->setOpciones($aOpciones);
        $oDesplZonas->setBlanco(FALSE);
        $aa_zonas = $oDesplZonas->getOpciones()->fetchAll(PDO::FETCH_ASSOC);
        break;
    default:
        $oZona = new Zona($Qid_zona);
        $nombre_zona = $oZona->getNombre_zona();
        $aa_zonas[0] = array('id_zona' => $Qid_zona, 'nombre_zona' => $nombre_zona);
}

$z = 0;
$GesZonasSacd = new GestorZonaSacd();
$aWhereZ = [];
$actividades_por_zona = [];
$cabeceras_por_zona = [];
foreach ($aa_zonas as $a_zonas) {
    $z++;
    $id_zona = $a_zonas['id_zona'];
    $nombre_zona = $a_zonas['nombre_zona'];
    $aWhereZ['id_zona'] = $id_zona;
    $cZonasSacd = $GesZonasSacd->getZonasSacds($aWhereZ);
    $p = 0;
    $actividades = [];
    $persona = [];
    foreach ($cZonasSacd as $oZonaSacd) {
        $aActivPersona = []; //inicializo el vector para la siguiente persona
        $id_nom = $oZonaSacd->getId_nom();

        $oSacd = new PersonaSacd($id_nom);
        if ($oSacd->getSituacion() !== 'A') {
            continue;
        }

        $ap_nom = $oSacd->getPrefApellidosNombre();
        $persona[$p] = "p#$id_nom#$ap_nom";

        if ($Qactividad === 'si') {
            $a = 0;
            $aWhereAct = [];
            $aOperadorAct = [];
            $aWhereAct['f_ini'] = "'$fin_iso'";
            $aOperadorAct['f_ini'] = '<=';
            $aWhereAct['f_fin'] = "'$inicio_iso'";
            $aOperadorAct['f_fin'] = '>=';
            if (!is_true($Qpropuesta)) {
                $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
            } else {
                $aWhereAct['status'] = ActividadAll::STATUS_BORRABLE;
                $aOperadorAct['status'] = '!=';
            }
            /*			
			$aWhere = ['id_nom' => $id_nom, 'plaza' => Asistente::PLAZA_PEDIDA];
			$aOperador = ['plaza' => '>='];
			*/
            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];

            $oGesActividadCargo = new GestorActividadCargo();
            $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $aAsistente['id_activ'];
                $propio = $aAsistente['propio'];
                $plaza = $aAsistente['plaza'];
                $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

                // Seleccionar sólo las del periodo
                $aWhereAct['id_activ'] = $id_activ;
                $GesActividades = new GestorActividad();
                $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
                if (is_array($cActividades) && count($cActividades) === 0) {
                    continue;
                }

                $oActividad = $cActividades[0]; // sólo debería haber una.
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $dl_org = $oActividad->getDl_org();
                $nom_activ = $oActividad->getNom_activ();

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActividad->getSfsvText();

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                //en el entorno de las primeras del día (a efectos del planning
                //ya es suficiente con la 1:16 de la madrugada)
                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = "1:16";
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = (string)$h_fin;


                if (is_true($Qpropuesta)) {
                    $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                    $nom_llarg = $nom_activ;
                } else {
                    if (ConfigGlobal::is_app_installed('procesos')) {
                        // Si tiene cargo sacd (se supone que planing_zonas sólo es para los sacd), que la fase 'ok_sacd' esté completada
                        // Si es asistente, que la fase ok_asistente esté completada.
                        $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                        $permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                    } else {
                        $permiso_ver = TRUE;
                    }

                    if ($permiso_ver === FALSE) {
                        continue;
                    }

                    // mirar permisos en la actividad.
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                }

                $css = PlanningStyle::clase($id_tipo_activ, $propio, $plaza, $oActividad->getStatus());

                $aActivPersona[] = array(
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => $id_tipo_activ,
                    'pagina' => '',
                    'id_activ' => $id_activ,
                    'propio' => $propio,
                    'plaza' => $plaza,
                    'css' => $css,
                );
                $a++;
            }
            // ++++++++++++++ Añado las ausencias +++++++++++++++
            $aWhereE = [];
            $aOperadorE = [];
            $aWhereE['id_nom'] = $id_nom;
            $aWhereE['f_ini'] = "'$fin_iso'";
            $aOperadorE['f_ini'] = '<=';
            $aWhereE['f_fin'] = "'$inicio_iso'";
            $aOperadorE['f_fin'] = '>=';
            $GesAusencias = new GestorEncargoSacdHorario();
            $cAusencias = $GesAusencias->getEncargoSacdHorarios($aWhereE, $aOperadorE);
            foreach ($cAusencias as $oTareaHorarioSacd) {
                $id_enc = $oTareaHorarioSacd->getId_enc();
                $oF_ini = $oTareaHorarioSacd->getF_ini();
                $oF_fin = $oTareaHorarioSacd->getF_fin();
                $h_ini = '';
                $h_fi = '';

                $oEncargo = new Encargo($id_enc);
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                $id = (string)$id_tipo_enc;
                if ($id[0] !== '7' && $id[0] !== '4') {
                    continue;
                }

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = '5:00';
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = empty($h_ini) ? '5:00' : (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = empty($h_fin) ? '22:00' : (string)$h_fin;

                $propio = "p";
                $nom_llarg = $oEncargo->getDesc_enc();
                $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
                if ($ini !== $fi) {
                    $nom_llarg .= " ($ini-$fi)";
                } else {
                    $nom_llarg .= " ($ini)";
                }

                $aActivPersona[] = array(
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => null,
                    'pagina' => '',
                    'id_activ' => $id_enc,
                    'propio' => $propio
                );
                $a++;

            }
            // ++++++++++++++++++++++++++++++++++++++++++++++++++
            // pongo el apellido como índice para poder ordenar por apellido.
            $actividades[$ap_nom] = array($persona[$p] => $aActivPersona);
            $p++;
        } else {
            $a_nom = [];
            // pongo el apellido como índice para poder ordenar por apellido.
            $actividades[$ap_nom] = array($persona[$p] => $a_nom);
            $p++;
        }
    }
    // ordenar las personas
    uksort($actividades, "strnatcasecmp"); // case insensitive
    /*
    lo que sigue es para que nos represente una linea en blanco al final:
    esto permite visualizar correctamente las 3 divisiones en los días
    en que todas las casas están ocupadas.
    */
    $actividades[] = array('###' => array());

    $actividades_por_zona[$z] = $actividades;
    $cabeceras_por_zona[$z] = $nombre_zona;
}

if ($z === 1) {
    $titulo = $cabeceras_por_zona[$z];
} else {
    $titulo = _("planning por zonas");
}

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/apps/planning/controller/leyenda.php?' . http_build_query(array('id_item' => 1)));

// ---------------- html ---------------------------------------------

echo $oPosicion->mostrar_left_slide(1);

echo "<button class='no_print' onclick=\"fnjs_exportar('html');\" >" . _("Descargar html") . "</button>";
echo "<button class='no_print' onclick=\"window.open('$goLeyenda','leyenda','width=400,height=500,screenX=200,screenY=200,titlebar=yes');\" >" . _("Ver leyenda") . "</button>";

echo "<div id=\"exportar\">";
switch ($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
        include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        break;
    case 3:
        include_once(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// Las variables de color de las columnas están en la página css.
include_once(ConfigGlobal::$dir_estilos . '/calendario_color_cols.css.php');
$oPlanning = new Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$oPlanning->setDd(3);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);


echo "<span id=\"span_exportar\"  title=\"$titulo\">";
for ($i = 1; $i <= $z; $i++) {
    $a_actividades = $actividades_por_zona[$i];
    $cabecera = $cabeceras_por_zona[$i];

    $oPlanning->setCabecera($cabecera);
    $oPlanning->setActividades($a_actividades);
    echo $oPlanning->dibujar();
}
echo "</div>"; //exportar