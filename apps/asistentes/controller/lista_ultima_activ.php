<?php 
use actividades\model\entity\Actividad;
use actividades\model\entity\GestorActividad;
use asistentes\model\entity\GestorAsistente;
use personas\model\entity\GestorPersonaS;
use ubis\model\entity\CentroDl;
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
*@package	delegacion
*@subpackage actividades
*@author	Josep Companys
*@since		15/08/03
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qcurso = (string) \filter_input(INPUT_POST, 'curso'); // actual, anterior
$Qid_ubi = (string) \filter_input(INPUT_POST, 'id_ubi');

$any=date("Y");

// Centros
$aWhereP = [];
$aOperadorP = [];
if (!empty($Qid_ubi) AND ($Qid_ubi != 999)){
    $oCentro = new CentroDl($Qid_ubi);
    $nombre_ubi = $oCentro->getNombre_ubi();
    $aWhereP['id_ctr'] = $Qid_ubi;
    $aWhereP['_ordre'] = 'apellido1, apellido2, nom';
} else { //todos los crt
    
}

//// FECHAS
$aWhereA = [];
$aOperadorA = [];
switch ($Qcurso) {
    case 'anterior':
        $any = $_SESSION['oConfig']->any_final_curs('crt') - 2 ;
        $QempiezaminIso = core\curso_est("inicio",$any,"crt")->format('Y-m-d');
        $QempiezamaxIso = core\curso_est("fin",$any,"crt")->format('Y-m-d');
        $fecha_ini = core\curso_est("ini",$any,"crt")->getFromLocal('-');
        $fecha_fin = core\curso_est("fin",$any,"crt")->getFromLocal('-');
        $titulo_fecha = sprintf(_("entre %s y %s"),$fecha_ini,$fecha_fin);
        break;
    case 'actual':
    default:
        $any = $_SESSION['oConfig']->any_final_curs('crt') - 1 ;
        $QempiezaminIso = core\curso_est("inicio",$any,"crt")->format('Y-m-d');
        $QempiezamaxIso = core\curso_est("fin",$any,"crt")->format('Y-m-d');
        $fecha = core\curso_est("fin",$any,"crt")->getFromLocal('-');
        $titulo_fecha = sprintf(_("tras la fecha %s"),$fecha);
        break;
}

//// Tipo Actvividad
switch ($Qque) {
	case "crt_s_sg":
	    $aWhereA['id_tipo_activ'] = '^1[45]1';
	    $aOperadorA['id_tipo_activ'] = '~';
	    if (empty($nombre_ubi)) {
            $titulo_actividad = _("s que todavía no han asistido a crt-s o crt-sg");
	    } else {
            $titulo_actividad = sprintf(_("s de %s que todavía no han asistido a crt-s o crt-sg"),$nombre_ubi);
	    }
		break;
    case "crt_s":
	    $aWhereA['id_tipo_activ'] = '^141';
	    $aOperadorA['id_tipo_activ'] = '~';
		$titulo_actividad = sprintf(_("s no celadores que han asistido por última vez a crt intern"));
		break;
    case "crt_cel":
	    $aWhereA['id_tipo_activ'] = '^141';
	    $aOperadorA['id_tipo_activ'] = '~';
		$titulo_actividad = sprintf(_("s celadores que han asistido por última vez a crt intern"));
        break;
    case "cv_s":
	    $aWhereA['id_tipo_activ'] = '^143';
	    $aOperadorA['id_tipo_activ'] = '~';
	    if (empty($nombre_ubi)) {
            $titulo_actividad = _("s que no han asistido a cv de s");
	    } else {
            $titulo_actividad = sprintf(_("s de %s que todavía no han asistido a cv de s"),$nombre_ubi);
	    }
        break;
    case "cv_s_ad":
	    $aWhereA['id_tipo_activ'] = '143002';
        $titulo_actividad = sprintf(_("s con ad reciente -entre 6 y 18 meses antes de la fecha última cv admisión del año- que todavía no han asistido a cv de ad"));
        
        //AND p.f_ad BETWEEN '$f_ad_min' AND '$f_ad_max'
        $aWhereP['inc'] = 'ad';
        $aWhereP['f_inc'] = "'',''";
        $aOperadorP['f_inc'] = 'BETWEEN';
    case "cv_jovenes":
        $titulo_actividad = _("s jóvenes (<30) que no han asistido a cv de s");
        $f_joven = date('Y-m-d', mktime(0,0,0,1,1,$any-30));
        //AND f_nacimiento > '$f_joven' AND ini_ce IS NULL AND fin_ce IS NULL
        $aWhereP['f_nacimineto'] = $f_joven;
        $aOperadorP['f_nacimineto'] = '>';
        $aWhereP['ce_ini'] = 'x';
        $aOperadorP['ce_ini'] = 'IS NULL';
        $aWhereP['ce_fin'] = 'x';
        $aOperadorP['ce_fin'] = 'IS NULL';
		break;
	break;
}

$titulo = $titulo_actividad.' '.$titulo_fecha;

$aWhereP['situacion'] = 'A';
$GesPersonasS = new GestorPersonaS();
$cPersonas = $GesPersonasS->getPersonasDl($aWhereP,$aOperadorP);
$i = 0;
$falta = 0;
$a_valores = [];
foreach ($cPersonas as $oPersona) {
    $i++;
    $id_nom = $oPersona->getId_nom();
    $ape_nom = $oPersona->getApellidosNombre();
    
    $GesAsistente = new GestorAsistente();
    $aWhereNom = ['id_nom'=>$id_nom];
    $aOperadorNom = [];
    $cAsistentes = $GesAsistente->getActividadesDeAsistente($aWhereNom,$aOperadorNom,$aWhereA,$aOperadorA,TRUE);
    if (count($cAsistentes) > 0) {
        reset($cAsistentes);
        $oAsistente = current($cAsistentes);
        $id_activ = $oAsistente->getId_activ();
        $oActividad = new Actividad($id_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oFini = $oActividad->getF_ini();
        $f_ini_iso = $oFini->getIso();
        if ($f_ini_iso > $QempiezaminIso) {
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

$a_cabeceras = [ ucfirst(_("apellidos, nombre")),
                ucfirst(_("centro")),
                ucfirst(_("fecha última asistencia")),
                ucfirst(_("tipo actividad")),
            ];

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);
//-------------------------------------- html -------------------------------------------------
?>
<h3><?= $titulo ?></h3>
<?php
printf(_('número de personas: %s de %s'),$falta,$i);
echo $oTabla->lista();
