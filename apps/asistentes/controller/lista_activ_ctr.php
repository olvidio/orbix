<?php 
use actividades\model as actividades;
use asistentes\model as asistentes;
use personas\model as personas;
use ubis\model as ubis;
/**
* Listados de los asistentes a actividades por ctr
*
* 
* 
*
*@package	delegacion
*@subpackage	personas
*@author	Josep Companys
*@since		15/5/02. modif 22/4/03 Dani para el caso de n que hacen el ca con agd.
*		
*/


/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$ssfsv = empty($_POST['ssfsv'])? '' : $_POST['ssfsv'];

if (core\ConfigGlobal::mi_sfsv() == 1 ) {
	if ($ssfsv == 'sf' && (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des")))) {
		$ssfsv = 'sf';
	} else {
		$ssfsv = 'sv';
	}
}
if (core\ConfigGlobal::mi_sfsv() == 2 ) {
	$ssfsv = 'sf';
}

$sasistentes = empty($_POST['sasistentes'])? '' : $_POST['sasistentes'];
$sactividad  = empty($_POST['sactividad'])? '' : $_POST['sactividad'];
empty($_POST['snom_tipo'])? $snom_tipo="" : $snom_tipo=$_POST['snom_tipo'];  

//echo "asistentes: $sasistentes<br>";
if ($_POST['n_agd']=="sss") { //no me cabe el valor en el menú en sss+ (pasa de 100 caracteres), por tanto se lo damos por programa
	$sasistentes="sss+";
} 
//desarrollamos la condición que filtre el tipo de actividad		
$condta = '';
$oTipoActiv= new web\TiposActividades();
$oTipoActiv->setSfsvText($ssfsv);
$oTipoActiv->setAsistentesText($sasistentes);
$oTipoActiv->setActividadText($sactividad);
// $oTipoActiv->setNom_tipoText($snom_tipo); // no té sentit
$condta=$oTipoActiv->getId_tipo_activ();

 //para el caso especial de n que hacen su ca en cv de agd:
$condta_plus = '';
if ($sasistentes=="n" && ($sactividad=="ca" || $sactividad=="crt")) {
	if ($sactividad=="ca") { $activ="cv"; } else { $activ=$sactividad; }
	$oTipoActiv= new web\TiposActividades();
	$oTipoActiv->setSfsvText($ssfsv);
	$oTipoActiv->setAsistentesText('agd');
	$oTipoActiv->setActividadText($activ);
	// $oTipoActiv->setNom_tipoText($snom_tipo); // no té sentit
	$condta_plus=$oTipoActiv->getId_tipo_activ();
} 

// para el caso de los ap. que han hecho el crt con sr.
$condta_sr='';
$oTipoActiv= new web\TiposActividades();
$oTipoActiv->setSfsvText($ssfsv);
$oTipoActiv->setAsistentesText('sr');
$oTipoActiv->setActividadText('crt');
// $oTipoActiv->setNom_tipoText($snom_tipo); // no té sentit
$condta_sr=$oTipoActiv->getId_tipo_activ();
//$condta1 = "($condta1 OR id_tipo_activ::text ~ '^$condta_sr')";

//echo "condta: $condta,condta_plus: $condta_plus,condta_sr: $condta_sr";
$condicion = '';
$condicion .= empty($condta)? '' : '^'.$condta;
$condicion .= empty($condta_plus)? '' : '|^'.$condta_plus;
$condicion .= empty($condta_sr)? '' : '|^'.$condta_sr;

$aWhereAct['id_tipo_activ'] = $condicion;
$aOperadorAct['id_tipo_activ'] = "~";

/*generamos el período de la búsqueda de actividades
en función de las condiciones que tengamos: */

$any=empty($_POST['year'])? date('Y') : $_POST['year'];

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


switch ($_POST['n_agd']) {
	case "a":
		$tabla="p_agregados";
		$aWhere['tipo_ctr']='a.';
		$aOperador['tipo_ctr']='~';
		break;
	case "n":
		$tabla="p_numerarios";
		$aWhere['tipo_ctr']='n.';
		$aOperador['tipo_ctr']='~';
		break;
	case "nm":
		$tabla="p_n_agd";
		$aWhere['tipo_ctr']='nm';
		$aOperador['tipo_ctr']='~';
		break;
	case "nj":
		$tabla="p_n_agd";
		$aWhere['tipo_ctr']='nj(ce)*';
		$aOperador['tipo_ctr']='~';
		break;
	case "sss":
		$tabla="p_sssc";
		$aWhere['tipo_ctr']='ss';
		$aOperador['tipo_ctr']='~';
		break;
	case "c": //otro
		$tabla="p_n_agd";
		$aWhere['id_ubi']=$_POST['id_ubi'];
		$aOperador['tipo_ctr']=array();
		break;
}
$aWhere['status']='t';
$aWhere['_ordre']='nombre_ubi';
// primero selecciono los centros y las personas que dependen de él
$GesCentros = new ubis\GestorCentroDl();
$cCentros = $GesCentros->getCentros($aWhere,$aOperador);

echo "<table>";
// Bucle para poder sacar los centros de la consulta anterior
$ctr=0;
foreach ($cCentros as $oCentro) {
	$ctr++;
	extract($oCentro->getTot());
	//consulta para buscar personas de cada ctr		
	// en caso de sss+ no hay campo situacion
	if ($tabla=="p_sssc") {
		$GesPersonas = new personas\GestorPersonaSSSC();
		$cPersonas = $GesPersonas->getPersonas(array('id_ctr'=>$id_ubi,'situacion'=>'A','_ordre'=>'apellido1'));
	} else {
		$GesPersonas = new personas\GestorPersonaDl();
		$cPersonas = $GesPersonas->getPersonas(array('id_ctr'=>$id_ubi,'situacion'=>'A','_ordre'=>'apellido1,apellido2,nom'));
	}	

	// salto de pagina:
    if ($ctr > 1) echo "<tr class='salta_pag'></td></td></tr>";
    echo "<tr><th colspan=4>$nombre_ubi</th></tr>";
    echo "<tr>
    <th>"._("nombre")."</th>
    <th>"._("actividades previstas")."</th>
    </tr>";

	// Bucle para poder sacar las personas de la consulta anterior	
	$i=0;
	$vidFam = '';
	foreach ($cPersonas as $oPersona) {
		$i++;
		$id_nom=$oPersona->getId_nom();
		$ap_nom=$oPersona->getApellidosNombre();
		if ($tabla!="p_sssc") {
			/*
			$vida=$oPersona->getVida_familia();
			//Pongo una linea en blanco entre los grupos de gente segun el campo de vida en familia
			//entre cl y scl de gr o dtor de est o sacd no cl
			if ($vidFam<="g" && $vida<="j" && $vida>"g"){
				$linea=1;
			//para alumnos 3er ano del ce
			} elseif($vidFam<="j" && $vida<="l" && $vida>"j"){
				$linea=1;
			//para sacd no de cl con cel en agd
			} elseif($vidFam<="j" && $vida<="p" && $vida>"j"){
				$linea=1;
			//para cel en agd
			} elseif($vidFam<="p" && $vida<="w" && $vida>"p"){
				$linea=1;
			//para residentes
			} elseif($vidFam<="q" && $vida<="s" && $vida>"q"){
				$linea=1;
			//para adscritos
			} elseif($vidFam<="w" && $vida<="z" && $vida>"w"){
				$linea=1;
			} else {
				$linea=0;
			}
			$vidFam= $vida;
			if ($linea==1) { $a_valores[] = "&nbsp;"; }
			*/
		}
		echo "<tr valign=\"TOP\"><td>$ap_nom</td>";
		//consulta de las actividades de cada persona
		/*$query_actividades="SELECT a.nom_activ,a.f_ini,a.f_fin   
					FROM d_cargos_activ da, a_actividades a 
					WHERE $condta1  da.id_nom=$id_nom AND da.id_activ=a.id_activ AND $periodo
					UNION
					SELECT a1.nom_activ, a1.f_ini,a1.f_fin  
					FROM d_asistentes_activ aa, a_actividades a1 
					WHERE $condta2  aa.id_nom=$id_nom AND aa.id_activ=a1.id_activ AND $periodo1
					ORDER BY 1";
		*/



		// Cambio 21/3/2007: sólo buscamos las propias:
		//$sCondicion = "propio='t' AND f_ini >= '$inicio' AND f_ini <= '$fin' AND $condta1";
		$aWhereNom['id_nom'] = $id_nom;
		$aWhereNom['propio'] = 't';
		$aWhereAct['f_ini'] = "'$inicio','$fin'";
		$aOperadorAct['f_ini'] = "BETWEEN";
		//$aWhereAct['id_tipo_activ'] = "^1[137]1";
		//$aOperadorAct['id_tipo_activ'] = "~";

		$GesAsistencias = new asistentes\GestorAsistente();
		$cAsistencias = $GesAsistencias->getActividadesDeAsistente($aWhereNom,$aWhereAct,$aOperadorAct);
		if (is_array($cAsistencias) && count($cAsistencias) == 0) {
			$nom_activ=_("Pendiente de solicitar");
			echo "<td><font style='color: red;'>$nom_activ</font></td></tr>";
		} else {
			$a=0;
			foreach ($cAsistencias as $oActividadAsistente) {
				$id_activ = $oActividadAsistente->getId_activ();
				$oActividad = new actividades\Actividad($id_activ);
				$nom_activ=$oActividad->getNom_activ();
				if ($a>0) {
					echo "<tr valign=\"TOP\"><td></td><td>$nom_activ</td></tr>";
				} else {
					echo "<td>$nom_activ</td></tr>";
				}
				$a++;
			}
		}
		/*
	   	$query_actividades="SELECT a1.nom_activ, a1.f_ini,a1.f_fin  
					FROM d_asistentes_activ aa JOIN a_actividades a1 USING (id_activ)
					WHERE aa.id_nom=$id_nom AND aa.propio='t' AND $periodo1 AND $condta2 
					ORDER BY f_ini";
		//echo "qqa: $query_actividades<br>";
		*/
		?>
		</td></tr>
		<?php
	}// nueva persona
} // nuevo centro
?>
</table>
