<?php 
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;
use ubis\model\entity as ubis;
/**
* Listado del plan de estudios por ctr
*
* 
* 
*
*@package	delegacion
*@subpackage	personas
*@author	Daniel Serrabou
*@since		3/6/03.
*		
*/


// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// sólo las actividades de estudios:

//generamos el período de la búsqueda de actividades
//en función de las condiciones que tengamos:
$hoy = date('d/m/Y');

empty($_POST['na'])? $na="" : $na=$_POST['na'];
$any=empty($_POST['year'])? date('Y')+1 : $_POST['year'];
if (empty($_POST['periodo']) || $_POST['periodo'] == 'otro') {
	$inicio = empty($_POST['inicio'])? $_POST['empiezamin'] : $_POST['inicio'];
	$fin = empty($_POST['fin'])? $_POST['empiezamax'] : $_POST['fin'];
} else {
	$oPeriodo = new web\Periodo();
	$oPeriodo->setAny($any);
	$oPeriodo->setPeriodo($_POST['periodo']);
	$inicio = $oPeriodo->getF_ini();
	$fin = $oPeriodo->getF_fin();
}

$aWhereNom['propio'] = 't';
$aWhereAct['f_ini'] = "'$inicio','$fin'";
$aOperadorAct['f_ini'] = 'BETWEEN';
$aWhereAct['id_tipo_activ'] = "'^1(12|33)'";
$aOperadorAct['id_tipo_activ'] = "~";

$aWhereCtr=array();
$aOperadorCtr=array();
switch ($_POST['n_agd']) {
	case "a":
		$tabla="p_agregados";
		$aWhereCtr['tipo_ctr'] = '^a';
		$aOperadorCtr['tipo_ctr'] = '~';
		break;
	case "n":
		$tabla="p_numerarios";
		$aWhereCtr['tipo_ctr'] = '^n';
		$aOperadorCtr['tipo_ctr'] = '~';
		break;
	case "nm":
		$tabla="p_n_agd";
		$aWhereCtr['tipo_ctr'] = 'nm';
		$aOperadorCtr['tipo_ctr'] = '~';
		break;
	case "nj":
		$tabla="p_n_agd";
		$aWhereCtr['tipo_ctr'] = 'nj(ce)*';
		$aOperadorCtr['tipo_ctr'] = '~';
		break;
	case "c":
		$tabla="p_n_agd";
		$aWhereCtr['id_ubi'] = $_POST['id_ubi'];
		$aOperadorCtr = array();
		break;
	default:
		$tabla="p_n_agd";
}

// primero selecciono los centros
$GesCentrosDl = new ubis\GestorCentroDl();
$cCentros = $GesCentrosDl->getCentros($aWhereCtr,$aOperadorCtr);
foreach ($cCentros as $oCentroDl) {
	$id_ubi = $oCentroDl->getId_ubi();
	$aGrupos[$id_ubi]= $oCentroDl->getNombre_ubi();

	$aWhere=array();
	$aOperador=array();
	$aWhere['situacion'] = 'A';
	$aWhere['id_ctr'] = $id_ubi;
	$aWhere['_ordre'] = 'apellido1,apellido2,nom';
	// Ahora (28.IV.2010) los agd quieren que salgan los sacd y los que no hacen estudios.
	$tipo_ctr=$oCentroDl->getTipo_ctr();
	if (substr($tipo_ctr,0,1)=="n") { // ctr de numerarios.
		$aWhere['sacd'] = 'f';
		$aWhere['stgr'] = 'n';
		$aOperador['stgr'] = '!=';
	}
	
	$GesPersonas = new personas\GestorPersonaDl();
	$cPersonas = $GesPersonas->getPersonas($aWhere,$aOperador);
	$i=0;
	foreach ($cPersonas as $oPersonaDl) {
		$i++;
		$id_nom = $oPersonaDl->getId_nom();
		$nom = $oPersonaDl->getApellidosNombre();
		$stgr = $oPersonaDl->getStgr();
		$a_valores[$id_ubi][$i][1]=$i;
		$a_valores[$id_ubi][$i][2]=$nom;
		
		//consulta de las actividades de cada persona
		//$condicion = "propio='t' AND $periodo AND id_tipo_activ::text ~ '^1(12|33)'";
		$aWhereNom['id_nom'] = $id_nom;
		$GesAsistente = new asistentes\GestorAsistente();
		$cAsistentes = $GesAsistente->getActividadesDeAsistente($aWhereNom,$aWhereAct,$aOperadorAct);
		$a=0;
		foreach ($cAsistentes as $oAsistente) {
			$a++;
			$id_activ=$oAsistente->getId_activ();
			$oActividad = new actividades\Actividad($id_activ);
			$nom_activ=$oActividad->getNom_activ();

			$f_ini=$oActividad->getF_ini();
			// los que ya lo han hecho:
			list($dia,$mes,$any) = preg_split('/[:\/\.-]/', $f_ini );
			$ini=mktime(0,0,0,$mes,$dia,$any);
			list($dia_h,$mes_h,$any_h) = preg_split('/[:\/\.-]/', $hoy );
			$ini_h=mktime(0,0,0,$mes_h,$dia_h,$any_h);
			if ($ini<$ini_h) { 
				$nom_activ=_("ya lo ha hecho");
				$asignaturas='';
			} else {
				switch($stgr) {
					case 'n': 
						$asignaturas=_("plan de formación"); 
						break;
					case 'r': 
						$asignaturas=_("repaso"); 
						break;
					default:
						$asignaturas='';
						$GesMatriculas = new actividadestudios\GestorMatricula();
						$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom,'id_activ'=>$id_activ));
						foreach ($cMatriculas as $oMatricula) {
							$id_asignatura = $oMatricula->getId_asignatura();
							$preceptor = $oMatricula->getPreceptor();
							$oAsignatura = new asignaturas\Asignatura($id_asignatura);
							$nombre_corto = $oAsignatura->getNombre_corto();
							$creditos = $oAsignatura->getCreditos();
							if ($preceptor == 't') { $preceptor = '(p)'; } else { $preceptor=''; }
							$asignaturas.= "$nombre_corto ($creditos "._("creditos")." )$preceptor<br>";
						}
				}
			}
			$a_valores[$id_ubi][$i][3]=$nom_activ;
			$a_valores[$id_ubi][$i][4]=$asignaturas;
		}	
	}
}

$a_cabeceras[]= _("nº");
$a_cabeceras[]= _("nombre");
$a_cabeceras[]= _("actividad");
$a_cabeceras[]= _("asignaturas");

asort($aGrupos);

$oLista = new web\Lista();
$oLista->setGrupos($aGrupos);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->listaPaginada();
?>
