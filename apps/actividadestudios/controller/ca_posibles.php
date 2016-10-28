<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;
use ubis\model as ubis;
/**
* Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		5/3/03.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//include_once(core\ConfigGlobal::$dir_estilos.'/cuadros_ca.css.php'); 

/**
 * Array con las asignaturas=>creditos, para no tener que consultar cada vez a la base de datos.
 *
 */
$GesAsignaturas = new asignaturas\GestorAsignatura();
$aAsigCreditos = $GesAsignaturas->getArrayAsignaturasCreditos();


function generar_nivel_stgr($id_tipo_activ) {
	$nivel_stgr = '';
	switch ($id_tipo_activ) {
		case 112000: //bienio
		case 112020:
		case 133000:
		case 133020:
			$nivel_stgr=1;
			break;
		case 112021: //cuadrienio
		case 112112: // semestre n
			$nivel_stgr=2;
			break;
		case 133021:
			$nivel_stgr=3;
			break;
		case 133105: // bienio y cuadrienio
			$nivel_stgr=10;
			break;
		case 112023: //repaso
		case 133023:
			$nivel_stgr=4;
			break;
		case 133016: // ceagd
			$nivel_stgr=5;
			break;
	}
	return $nivel_stgr;
}

function contar_creditos($id_nom,$asignaturas) {
	$suma_creditos=0;
	$GesNotas = new notas\GestorNota();
	$cNotas = 	$GesNotas->getNotas(array('superada'=>'t'));
	$aSuperadas = array();
	foreach ($cNotas as $oNota) {
		$id_situacion = $oNota->getId_situacion();
		$aSuperadas[$id_situacion] = 't';
	}
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_nom));
	$a=0;
	$todas_asig_p = array();
	foreach ($cPersonaNotas as $oPersonaNota) {
		$id_situacion = $oPersonaNota->getId_situacion();
		$id_asignatura = $oPersonaNota->getId_asignatura();
		if (array_key_exists($id_situacion,$aSuperadas)) {
			$todas_asig_p[]=$id_asignatura;
		}
	}
	foreach( $asignaturas as $id_asignatura => $creditos ) {
		if (!in_array( $id_asignatura, $todas_asig_p)) { $suma_creditos += $creditos; }
	}
	return $suma_creditos;
}


// -----------------------------------------------------------------------------------------

$obj_pau = empty($_POST['obj_pau'])? '' : $_POST['obj_pau'];
// vengo directamente con un id:
if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$na=strtok("#"); // id_tabla
	$_POST['todos']=1;
	$empiezamin=date("d/m/Y");
	if (date("m") < 10 ) {
		$empiezamax="30/10/".date("Y");
	} else {
		$next_year=date("Y")+1;
		$empiezamax="30/10/".$next_year;
	}
	$condicion = " f_ini BETWEEN '$empiezamin' AND '$empiezamax' AND";
	$oPosicion->addParametro('id_sel',$id_sel);
} else {
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
	$condicion = " f_ini BETWEEN '$inicio' AND '$fin' AND";
	$aWhere['f_ini'] = "'$inicio','$fin'";
	$aOperador['f_ini'] = 'BETWEEN';
}
if ($_POST['todos']!=1) {
	$grupo_estudios = $_POST['todos'];
	$GesGrupoEst = new ubis\GestorDelegacion();
	$cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios'=>$grupo_estudios));
	if (count($cDelegaciones) > 1) $aOperador['dl_org'] = 'OR';
	$mi_grupo = '';
	foreach ($cDelegaciones as $oDelegacion) {
		$mi_grupo .= empty($mi_grupo)? '' : ',';
		$mi_grupo .= "'".$oDelegacion->getDl()."'";
	}
	$aWhere['dl_org'] = $mi_grupo;
}

$aWhere['status'] = 2;
$aWhere['_ordre'] = 'nivel_stgr,f_ini';

switch ($na) {
	case "agd":
	case "a":
		//caso de agd
		$id_ctr = empty($_POST['id_ctr_agd'])? '' : $_POST['id_ctr_agd'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$tabla_pau='p_agregados';

		$aWhere['id_tipo_activ'] = '^133';
		$aOperador['id_tipo_activ'] = '~';
		$GesActividades = new actividades\GestorActividadPub();
		$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
		break;
	case "n":
		// caso de n
		$id_ctr = empty($_POST['id_ctr_n'])? '' : $_POST['id_ctr_n'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='n';
		$tabla_pau='p_numerarios';
	
		$aWhere['id_tipo_activ'] = '^112';
		$aOperador['id_tipo_activ'] = '~';
		$GesActividades = new actividades\GestorActividadPub();
		$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
		/*
		$sQuery="SELECT * FROM a_actividades
  				WHERE status = 2 AND $condicion $zona
				(id_tipo_activ = 112000 OR id_tipo_activ = 112020 OR id_tipo_activ = 112021 OR id_tipo_activ = 112023 OR id_tipo_activ = 112112)
				ORDER BY nivel_stgr,f_ini";
		//echo "query: $sQuery<br>";
		$GesActividades = new actividades\GestorActividad();
		$cActividades = $GesActividades->getActividadesQuery($sQuery);
		*/
	break;
}
//set_time_limit(0);
// per les lletres verticals
$gruix=20;
$alt=300;

// -------------------------- lista de ca con sus asignaturas --------------------------
//Si accedo via formulario, debo poner los ca escogidos; y sino los de las dlb, dlz, dlva
$sql_where="";
if (!empty($_POST['idca'])){
       
} else { //no vengo del formulario: es para todos los ca de la zona.
	$asig_ca=array();
	$i=0;
	$max_len_activ = 1;
	foreach ($cActividades as $oActividad) {
		$i++;
		extract($oActividad->getTot());
		// cambio el nombre de la actividad: borro cosas:
		$nom_activ=str_replace ("ca n","", $nom_activ);
		$nom_activ=str_replace ("bienio","", $nom_activ);
		$nom_activ=str_replace ("cuadrienio","", $nom_activ);
		$nom_activ=str_replace ("repaso","", $nom_activ);
		$nom_activ=str_replace ("semestre","", $nom_activ);
		$nom_activ=trim ($nom_activ);
		
		if (empty($nivel_stgr)) { 
			printf(_("El ca: %s no tiene puesto el nivel de stgr.")."<br>",$nom_activ);
			$nivel_stgr=generar_nivel_stgr($id_tipo_activ); 
		}
		if ($nivel_stgr==4 || $nivel_stgr==9 || $nivel_stgr==8 || $nivel_stgr==7) {  // repaso, mayores 30, menores 30, pa-ad
			$asignaturas=array("dd");
		} else {
			// por cada ca creo un array con las asignaturas y los créditos.
			$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignatura();
			$cActividadAsignaturas = $GesActividadAsignaturas->getActividadAsignaturas(array('id_activ'=>$id_activ,'tipo'=>'NULL'),array('tipo'=>'IS NULL'));
			$m=0;
			$asignaturas=array();
			foreach ( $cActividadAsignaturas as $oActividadAsignatura) {
				$m++;
				$id_asignatura = $oActividadAsignatura->getId_asignatura();
				$asignaturas[$id_asignatura]=$aAsigCreditos[$id_asignatura];
			}
			if ($m==0 && $nivel_stgr) {
				printf(_("El ca: %s no tiene puesta ninguna asignatura.")."<br>",$nom_activ);
				continue;
			}
		}
		$asig_ca[]=array( 'id_activ'=>$id_activ,
						'nom_activ'=>$nom_activ,
						'nivel_stgr'=>$nivel_stgr,
						'asignaturas'=>$asignaturas
							);
		/* Ya ha hecho posible con css
		// codifico el nombre_activ a iso porque no me lo dibuja bien:
		$nom_activ_iso=iconv("UTF-8","ISO-8859-1",$nom_activ);
		// grabo un dibujo temporal con el nombre de la actividad en vertical
		$im  = imagecreatetruecolor ($gruix, $alt);
		$negre = imagecolorallocate ($im, 0, 0, 0);
		$blanc = imagecolorallocate ($im, 255, 255, 255);
		imagecolortransparent($im, $negre);

		// Escribir el texto
		imagestringup ($im,3, 3, $alt-10, $nom_activ_iso, $blanc);

		// Guardar la imagen
		imagepng($im, core\ConfigGlobal::$dir_web."/log/tmp/$id_activ");
		imagedestroy($im);
		*/
		$len = strlen($nom_activ);
		$max_len_activ = ($max_len_activ < $len)? $len : $max_len_activ;
	}
}

// ----------------------- Ahora la lista de personas -----------------

/* Según si selecciono por centros, o por personas individuales */
$aWhere = array();
$aOperador = array();
if (!empty($_POST['sel'])) { //vengo de un checkbox
	$alum=0;
	$id_nom_lst='';
	foreach ($_POST['sel'] as $selBox) {
		$id_nom=strtok($selBox,"#");
		if ($alum > 0) $id_nom_lst.="|";
		if (!empty($id_nom)) $id_nom_lst.='^'.$id_nom.'$';
		$alum++;
	}

	$aWhere['id_nom']=$id_nom_lst;
	$aOperador['id_nom']='~';
	$aWhere['_ordre']='apellido1,apellido2,nom';

	$_POST['texto']="image";
} else {
	$aWhere['situacion']='A';
	$aWhere['sacd']='f'; // que no salgan los sacd
	$aWhere['id_tabla']=$id_tabla_persona;
	if ($id_tabla_persona == 'n') {
		$aWhere['stgr']='n';
		$aOperador['stgr']='<>';
	}
	$aWhere['_ordre']='id_ctr,apellido1,apellido2,nom';
	if (!empty($id_ctr)) $aWhere['id_ctr']=$id_ctr;
}

$GesPersonaDl = new personas\GestorPersonaDl();
$cPersonas = $GesPersonaDl->getPersonas($aWhere,$aOperador);

// El bucle: para cada alumno miro los creditos posibles para cada ca
$a=0;
$cuadro=array();
if (!empty($_POST['sel'])) { //vengo de un checkbox
	/* para hacerlo compatible con el caso de los centros. miro ahora el nombre del ctr */
	$cOrdPersonas = array();
	foreach ($cPersonas as $oPersonaDl) {
		$id_ubi=$oPersonaDl->getId_ctr();
		$oUbi = new ubis\CentroDl($id_ubi);
		$Ctr = $oUbi->getNombre_ubi();
		// para ordenar paso a minúsculas.
		$ctr = strtolower($Ctr);
		$cOrdPersonas[$ctr][] = array('Ctr'=>$Ctr,'oPersonaDl'=>$oPersonaDl);
	}
} else {
	/* para ordenar por orden alfabético de ctr */
	$cOrdPersonas = array();
	$id_ubi_old = '';
	foreach ($cPersonas as $oPersonaDl) {
		$id_ubi=$oPersonaDl->getId_ctr();
		if ($id_ubi != $id_ubi_old) {
			$oUbi = new ubis\CentroDl($id_ubi);
			$Ctr = $oUbi->getNombre_ubi();
			// para ordenar paso a minúsculas.
			$ctr = strtolower($Ctr);
		}
		$cOrdPersonas[$ctr][] = array('Ctr'=>$Ctr,'oPersonaDl'=>$oPersonaDl);
	}
}
ksort($cOrdPersonas);
foreach ($cOrdPersonas as $ctr=>$ctrPersonas) {
	foreach ($ctrPersonas as $row) {
		$a++;
		$Ctr=$row['Ctr'];
		$oPersonaDl = $row['oPersonaDl'];
		$id_nom=$oPersonaDl->getId_nom();
		$id_tabla_persona=$oPersonaDl->getId_tabla();
		$nom=$oPersonaDl->getApellidosNombre();
		/*
		$id_ubi=$oPersonaDl->getId_ctr();
		$oUbi = new CentroDl($id_ubi);
		$ctr = $oUbi->getNombre_ubi();
		*/
		$stgr=$oPersonaDl->getStgr(); //posibles: n,s,t,b,c1,c2,r
		//$ce=$oPersonaDl->getCe(); //está en el ce? 1,2,3
		$ce='';
		//echo "persona: $id_nom,$nom,$ctr,$stgr<br>";
		$creditos=0;
		// por cada ca:
		$actividades=array();
		foreach( $asig_ca as $asig_ca_2 ) {
			$id_activ=$asig_ca_2["id_activ"];
			$nom_activ=$asig_ca_2["nom_activ"];
			$nivel_stgr=$asig_ca_2["nivel_stgr"];
			$asignaturas=$asig_ca_2["asignaturas"];
			
			// para el caso especial de agd en el ceagd
			if ($ce && $na=="agd") { $stgr="ce"; }
		
			switch ($stgr) {
				case "n":
						if ($nivel_stgr==9 || $nivel_stgr==8 || $nivel_stgr==7) { $creditos='x'; } else { $creditos="-"; }
						break;
				case "b":
						if ($nivel_stgr==1) { $creditos=contar_creditos($id_nom,$asignaturas); } else { $creditos="-"; }
						break;
				case "c1":
						if ($nivel_stgr==2) { $creditos=contar_creditos($id_nom,$asignaturas); 
						} else if ($nivel_stgr==3) { $creditos=contar_creditos($id_nom,$asignaturas); 
						} else { $creditos="-"; }
						break;
				case "c2":
						if ($nivel_stgr==3) { $creditos=contar_creditos($id_nom,$asignaturas); } else { $creditos="-"; }
						break;
				case "r":
						if ($id_tabla_persona=='n') {
						   if ($nivel_stgr==4) { $creditos="x"; } else { $creditos="-"; }
						} else {
							if ($nivel_stgr==4 || $nivel_stgr==9 || $nivel_stgr==8 || $nivel_stgr==7) { $creditos='x'; } else { $creditos="-"; }
						}
						break;
				case "ce":
						if ($nivel_stgr==5) { $creditos=contar_creditos($id_nom,$asignaturas); } else { $creditos="-"; }
						break;
			}
			$actividades[]=array(	'id_activ'=>$id_activ,
									'nom_activ'=>$nom_activ,
									'creditos'=>$creditos,
									'nivel_stgr'=>$nivel_stgr
								);				
		}
		$cuadro[]=array(	'id_nom'=>'id_nom',
						'nom'=>$nom,
						'ctr'=>$Ctr,
						'stgr'=>$stgr,
						'actividades'=>$actividades
					);
		$actividades=array();
	}
}

// -----------------------------  cabecera ---------------------------------
$form_action=core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php';

// -------------------------- si es una persona, saco una lista. -----------------------
if (!empty($_POST['sel']) && $alum==1) { //vengo de un 'checkbox' => sólo una persona
	//$pagina=core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?pau=p&que=activ&id_pau='.$id_nom.'&tabla_pau='.$tabla_pau.'&id_dossier=1301y1302';
	$aParamGo=array('que'=>'activ','pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau,'id_dossier'=>'1301y1302');
	$pagina=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($aParamGo));

	$f=0;
	$a1=array();
	foreach($cuadro as $a1) {
		$f++;
		$ctr=$a1["ctr"];
		$nom=$a1["nom"];
		$stgr=$a1["stgr"];
		$actividades=$a1["actividades"];
	//las filas
		echo $oPosicion->atras();
		echo "<table>";
		echo "<th class=nom colspan=2>posibles ca de $nom ($ctr)</th><th>stgr: $stgr</th>";
		foreach($actividades as $a3) {
				   $nom_activ=$a3["nom_activ"];
				   $creditos=$a3["creditos"];
				   $nivel_stgr=$a3["nivel_stgr"];
				   switch ($nivel_stgr) {
					case 1:
						$est=_("bienio");
						break;
					case 2:
						$est=_("cuadrienio-I");
						break;
					case 3:
						$est=_("cuadrienio-II-IV");
						break;
					case 4: 
						$est=_("repaso");
						break;
					case 5: 
						$est=_("ce");
						break;
					}
				   echo "<tr><td>$est</td><td style=\"text-align: left;\">  $nom_activ</td><td>$creditos</td></tr>";
				}
	}
	echo "</table>";
	echo "<h3><span class=link onclick=\"fnjs_update_div('#main','$pagina')\" >". _("ir a dossier de actividades")."</span></h3>";
} else {
// -------------------------- si es para el centro/s saco una tabla -------------------------
	// Dibujar la tabla
	// la cabecera es la misma para todos: ----------------------------------------------------
	$cabecera="";
	$titulo="";
	$sub_titulo="";
	$colgroups="<colgroup span=1 width=200></colgruoup>"; 
	$nivel_stgr_old="";
	$num_cols=0;
	$cols2=0;
	foreach($asig_ca as $a2) {
	   $nom_activ=$a2["nom_activ"];
	   $id_activ=$a2["id_activ"];
	   $nivel_stgr=$a2["nivel_stgr"]; // para cada nivel columnas distintas
	   if ($nivel_stgr_old!=$nivel_stgr) {
	   		if ($num_cols!=0) {
				$colgroups.="<colgroup span=$num_cols width=$gruix></colgruoup>";
				switch ($nivel_stgr_old) {
					case 1:
						$est=_("bienio");
						$est_2="";
						$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
						break;
					case 2:
						$est=_("cuadrienio");
						$est_2=_("año I");
						$cols2=$num_cols;
						break;
					case 3:
						$est=_("cuadrienio");
						// canvi 30.4.2009
						//if ($na=="n") $est_2=_("años II-IV");
						$est_2=_("años II-IV");
						$cols_c=$cols2+$num_cols;
						$titulo.="<th class='calendario' colspan=$cols_c style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
						break;
					case 4: 
						$est=_("repaso");
						$est_2="";
						$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
						break;
					case 5: 
						$est=_("ce");
						$est_2="";
						$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
						break;
					default: 
						$est=_("otros");
						$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
						break;
				}
				$sub_titulo.="<th class='calendario' colspan=$num_cols>$est_2</th>";
			}
			$num_cols=0;
			$nivel_stgr_old=$nivel_stgr;
		}
		$num_cols++;
		switch ($_POST['texto']) {
			case "text":
				$cabecera .= "<th class='calendario' >".ucfirst($nom_activ)."</th>";
				break;
			case "image":
	   			//$cabecera .= "<th><img src='".core\ConfigGlobal::getWeb()."/log/tmp/$id_activ' border=0 alt='".ucfirst($nom_activ)."'></th>";
				$height = $max_len_activ * 7;
	   			$cabecera .= "<th class='vertical2 calendario' height=$height ><div class='vertical'>".ucfirst($nom_activ)."</div></th>";
				break;
		}
	}
	$est_2='';
	$cols2=0;
	// para el último grupo
	switch ($nivel_stgr_old) {
		case 1:
			$est=_("bienio");
			$titulo.="<th class='calendario' colspan=$num_cols>$est</th>";
			break;
		case 2:
			$est=_("cuadrienio");
			$est_2=_("año I");
			$cols2=$num_cols;
			break;
		case 3:
			$est=_("cuadrienio");
			// canvi 30.4.2009
			//if ($na=="n") $est_2=_("años II-IV");
			$est_2=_("años II-IV");
			$cols_c=$cols2+$num_cols;
			$titulo.="<th class='calendario' colspan=$cols_c style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
			break;
		case 4: 
			$est=_("repaso");
			$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
			break;
		case 5: 
			$est=_("ce");
			$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
			break;
		default: 
			$est=_("otros");
			$titulo.="<th class='calendario' colspan=$num_cols style='border-color: black; border-style: solid; border-width: thin;'>$est</th>";
			break;
	}
	$colgroups.="<colgroup span=$num_cols width=$gruix></colgruoup>";
	$sub_titulo.="<th class='calendario' colspan=$num_cols>$est_2</th>";
	// fin del último -----------------------
	$cabecera_tabla= "$colgroups<tbody>
		<tr>
	  		<td></td>$titulo</tr>
		<tr>
	  		<td></td>$sub_titulo</tr>
		<tr>
	  		<td></td>$cabecera</tr></tbody><tbody>"; 	
	// fin cabecera -----------------------------------------------------------------
	$_POST['ref'] = empty($_POST['ref'])? '' : $_POST['ref'];
	$f=0;
	$a1=array();
	$ctr_old="";
	foreach($cuadro as $a1) {
		$f++;
		$ctr=$a1["ctr"];
		$nom=$a1["nom"];
		$actividades=$a1["actividades"];
		if ($ctr_old!=$ctr) { //nueva tabla
			$ctr_old=$ctr;
			if ($f!=1) { echo "</table><br>"; }
	   		echo "<div class='A4'><table><tr><th align='LEFT'>$ctr</th><th align='RIGHT'>".$_POST['ref']."</th></tr></table>
					<table rules='groups' CELLSPACING=0>
					";
			echo $cabecera_tabla;
		}
		//las filas
		echo "<tr><td class=nom>$nom</td>";
		foreach($actividades as $a3) {
				   $creditos=$a3["creditos"];
				   echo "<td>$creditos</td>";
				}
		echo "</tr>";
	}
	echo "</tbody></table>";
?>
<br>
<table CELLSPACING=5><tr><td valign=top class=observ><?php echo _("observaciones").":"; ?></td>
<td class=observ><textarea class=observ name="observ" cols="120" rows="5"></textarea></td>
</tr>
</table>
</div>
</body></html>
<?php
} // fin del if id_nom (dibujar tabla)
