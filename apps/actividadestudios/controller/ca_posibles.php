<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;
use ubis\model\entity as ubis;
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

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosiblesCa = new actividadestudios\PosiblesCa(); 

$oPosicion->recordar();
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}

$obj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qtodos = (string) \filter_input(INPUT_POST, 'todos');
$Qtexto = (string) \filter_input(INPUT_POST, 'texto');
$Qref = (string) \filter_input(INPUT_POST, 'ref');
$Qidca = (string) \filter_input(INPUT_POST, 'idca');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// vengo directamente con un id:
if (!empty($a_sel)) { //vengo de un checkbox
	$Qid_nom = strtok($a_sel[0],"#");
	$Qna=strtok("#"); // id_tabla
	$Qtodos=1;
	$inicio=date("d/m/Y");
	if (date("m") < 10 ) {
		$fin="30/10/".date("Y");
	} else {
		$next_year=date("Y")+1;
		$fin="30/10/".$next_year;
	}
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
	$Qid_ctr_agd = 0;
	$Qid_ctr_n = 0;
} else {
	$Qid_ctr_agd = (integer) \filter_input(INPUT_POST, 'id_ctr_agd');
	$Qid_ctr_n = (integer) \filter_input(INPUT_POST, 'id_ctr_n');
	$Qna = (string) \filter_input(INPUT_POST, 'na');
	$Qyear = (integer) \filter_input(INPUT_POST, 'year');
	$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');

	if (empty($Qid_ctr_agd) && empty($Qid_ctr_n)) { 
		$msg_txt = _("Debe seleccionar un centro o grupo de centros");
		exit($msg_txt);
	}
	$any=empty($Qyear)? date('Y')+1 : $Qyear;
	
	if ($Qperiodo == 'otro') {
		$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
		$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
		$Qfin = (string) \filter_input(INPUT_POST, 'fin');
		$inicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
		$fin = empty($Qfin)? $Qempiezamax : $Qfin;
	} else {
		$periodo = empty($Qperiodo)? 'curso_ca' : $Qperiodo;
		$oPeriodo = new web\Periodo();
		$oPeriodo->setAny($any);
		$oPeriodo->setPeriodo($periodo);
		$inicio = $oPeriodo->getF_ini();
		$fin = $oPeriodo->getF_fin();
	}
}

switch ($Qna) {
	case "agd":
	case "a":
		//caso de agd
		$id_ctr = ($Qid_ctr_agd==1)? '' : $Qid_ctr_agd; //si es 1 es todos los ctr.
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$tabla_pau='p_agregados';

		$aWhereActividad['id_tipo_activ'] = '^133';
		$aOperadorActividad['id_tipo_activ'] = '~';
		break;
	case "n":
		// caso de n
		$id_ctr = ($Qid_ctr_n==1)? '' : $Qid_ctr_n; //si es 1 es todos los ctr.
		$id_tabla_persona='n';
		$tabla_pau='p_numerarios';
	
		$aWhereActividad['id_tipo_activ'] = '^112';
		$aOperadorActividad['id_tipo_activ'] = '~';
	break;
}

// ----------------------- Selección de personas -----------------

/* Según si selecciono por centros, o por personas individuales */
$aWhere = array();
$aOperador = array();
$alum=0;
if (!empty($a_sel)) { //vengo de un checkbox
	$id_nom_lst='';
	foreach ($a_sel as $selBox) {
		$id_nom=strtok($selBox,"#");
		if ($alum > 0) $id_nom_lst.="|";
		if (!empty($id_nom)) $id_nom_lst.='^'.$id_nom.'$';
		$alum++;
	}

	$aWhere['id_nom']=$id_nom_lst;
	$aOperador['id_nom']='~';
	$aWhere['_ordre']='apellido1,apellido2,nom';

	$Qtexto="image";
	$GesPersonaDl = new personas\GestorPersonaDl();
} else {
	switch ($id_tabla_persona) {
		case 'n':
			$aWhere['stgr']='n';
			$aOperador['stgr']='<>';
			$GesPersonaDl = new personas\GestorPersonaN();
			break;
		case 'a':
			$GesPersonaDl = new personas\GestorPersonaAgd();
			break;
	}
	$aWhere['situacion']='A';
	$aWhere['sacd']='f'; // que no salgan los sacd
	$aWhere['id_tabla']=$id_tabla_persona;
	$aWhere['_ordre']='id_ctr,apellido1,apellido2,nom';
	if (!empty($id_ctr)) $aWhere['id_ctr']=$id_ctr;
}

$cPersonas = $GesPersonaDl->getPersonas($aWhere,$aOperador);

//------------- Selección de Actividades ---------------------------------
$aWhereActividad['f_ini'] = "'$inicio','$fin'";
$aOperadorActividad['f_ini'] = 'BETWEEN';

if ($Qtodos!=1) {
	$grupo_estudios = $Qtodos;
	$GesGrupoEst = new ubis\GestorDelegacion();
	$cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios'=>$grupo_estudios));
	if (count($cDelegaciones) > 1) $aOperadorActividad['dl_org'] = 'OR';
	$mi_grupo = '';
	foreach ($cDelegaciones as $oDelegacion) {
		$mi_grupo .= empty($mi_grupo)? '' : ',';
		$mi_grupo .= "'".$oDelegacion->getDl()."'";
	}
	$aWhereActividad['dl_org'] = $mi_grupo;
}

$aWhereActividad['status'] = 2;
$aWhereActividad['_ordre'] = 'nivel_stgr,f_ini';

$cActividades = array();
$GesActividades = new actividades\GestorActividadPub();
$cActividades = $GesActividades->getActividades($aWhereActividad,$aOperadorActividad);

// per les lletres verticals
$gruix=20;

// lista de ca con sus asignaturas
//Si accedo via formulario, debo poner los ca escogidos; y sino los de las dlb, dlz, dlva
$sql_where = '';
$msg_txt = '';
if (!empty($Qidca)){
       
} else { //no vengo del formulario: es para todos los ca de la zona.
	$a_datos_ca=array();
	$i=0;
	$max_len_activ = 1;
	$nc_bienio=0;
	$nc_cuadrienio1=0;
	$nc_cuadrienio2=0;
	$nc_repaso=0;
	$nc_ce=0;
	$nc_otros=0;
	foreach ($cActividades as $oActividad) {
		$asignaturas=array();
		$i++;
		//extract($oActividad->getTot());
		$id_activ = $oActividad->getId_activ();
		$nom_activ = $oActividad->getNom_activ();
		$nivel_stgr = $oActividad->getNivel_stgr();
		// si es sólo un alumno pongo el nombre entero, porque saldrá en formato lista.
		// sino, cambio el nombre de la actividad: borro cosas:
		if ($alum > 1) {
			$nom_activ=str_replace ("ca n","", $nom_activ);
			$nom_activ=str_replace ("bienio","", $nom_activ);
			$nom_activ=str_replace ("cuadrienio","", $nom_activ);
			$nom_activ=str_replace ("repaso","", $nom_activ);
			$nom_activ=str_replace ("semestre","", $nom_activ);
			$nom_activ=trim ($nom_activ);
		}
		if (empty($nivel_stgr)) { 
			$msg_txt .= sprintf(_("El ca: %s no tiene puesto el nivel de stgr.")."<br>",$nom_activ);
			$nivel_stgr=$oActividad->generarNivelStgr(); 
		}
		// repaso, mayores 30, menores 30, pa-ad
		if ($nivel_stgr==4 || $nivel_stgr==9 || $nivel_stgr==8 || $nivel_stgr==7) { 
			$asignaturas=array("dd");
		} else {
			// por cada ca creo un array con las asignaturas y los créditos.
			$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignatura();
			$asignaturas = $GesActividadAsignaturas->getAsignaturasCa($id_activ);
			if (count($asignaturas)==0 && $nivel_stgr) {
				$msg_txt .= sprintf(_("El ca: %s no tiene puesta ninguna asignatura.")."<br>",$nom_activ);
				continue;
			}
		}
	   switch ($nivel_stgr) {
		case 1:  // bienio
			$nc_bienio++;
			break;
		case 2: // cuadrienio-I
			$nc_cuadrienio1++;
			break;
		case 3: //cuadrienio-II-IV
			$nc_cuadrienio2++;
			break;
		case 4: // repaso
			$nc_repaso++;
			break;
		case 5: // ce
			$nc_ce++;
			break;
		default: 
			$nc_otros++;
			break;
		}

		$a_datos_ca[$id_activ]=array(
						'nom_activ'=>$nom_activ,
						'nivel_stgr'=>$nivel_stgr,
						'asignaturas'=>$asignaturas
						);
		
		$len = strlen($nom_activ);
		$max_len_activ = ($max_len_activ < $len)? $len : $max_len_activ;
	}
	$nc_cuadrienio = $nc_cuadrienio1 + $nc_cuadrienio2;
}

// ------------  El bucle: para cada alumno miro los creditos posibles para cada ca
$a=0;
$cuadro=array();
if (!empty($a_sel)) { //vengo de un checkbox
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
		$nom_persona=$oPersonaDl->getApellidosNombre();
		/*
		$id_ubi=$oPersonaDl->getId_ctr();
		$oUbi = new CentroDl($id_ubi);
		$ctr = $oUbi->getNombre_ubi();
		*/
		$stgr=$oPersonaDl->getStgr(); //posibles: n,s,t,b,c1,c2,r
		
		if (method_exists($oPersonaDl,'getCe')) {
			$ce=$oPersonaDl->getCe(); //está en el ce? 1,2,3
		} else {
			$ce = '';
		}
		//echo "persona: $id_nom,$nom,$ctr,$stgr<br>";
		$creditos=0;
		// por cada ca:
		$aActividades=array();
		foreach( $a_datos_ca as $id_activ => $datos_ca ) {
			$nom_activ=$datos_ca["nom_activ"];
			$nivel_stgr=$datos_ca["nivel_stgr"];
			$asignaturas=$datos_ca["asignaturas"];
			
			// para el caso especial de agd en el ceagd
			if ($ce && $Qna=="agd") { $stgr="ce"; }
		
			// Contar creditos:
			switch ($stgr) {
				case "n":
						if (in_array($nivel_stgr, [9,8,7])) {
							$creditos='x';
						} else {
							$creditos="-";
						}
						break;
				case "b":
						if ($nivel_stgr==1) {
							$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas);
						} else {
							$creditos="-";
						}
						break;
				case "c1":
						if ($nivel_stgr==2) {
							$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas); 
						} elseif ($nivel_stgr==3) {
							$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas); 
						} else {
							$creditos="-";
						}
						break;
				case "c2":
						if ($nivel_stgr==3) {
							$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas);
						} else {
							$creditos="-";
						}
						break;
				case "r":
						if ($id_tabla_persona=='n') {
						   if ($nivel_stgr==4) { $creditos="x"; } else { $creditos="-"; }
						} else {
							if (in_array($nivel_stgr, [4,9,8,7])) {
								$creditos='x';
							} else {
								$creditos="-";
							}
						}
						break;
				case "ce":
						if ($nivel_stgr==5) {
							$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas);
						} else {
							$creditos="-";
						}
						break;
			}
			
			$aActividades[$id_activ]=array(	
									'nom_activ' => $nom_activ,
									'creditos' => $creditos,
								);				
		}
		
		$cuadro[$Ctr][$nom_persona]=array(
						'stgr'=>$stgr,
						'aActividades'=>$aActividades
					);
	}
}

// -------------------------- si es una persona, saco una lista. -----------------------
if (!empty($a_sel) && $alum==1) { //vengo de un 'checkbox' => sólo una persona
	$aParamGo=array('que'=>'activ','pau'=>'p','id_pau'=>$id_nom,'obj_pau'=>$obj_pau,'id_dossier'=>'1301y1302');
	$pagina=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($aParamGo));

	// Errores y falta de información
	if (count($cuadro) > 1) {
		exit(_("Sólo debebería haber uno"));
	}
	if (!empty($msg_txt)) { 
		echo "<div class='no_print'>$msg_txt</div>"; 
	} 

	foreach($cuadro as $ctr => $datos_persona) {
		$nom = key($datos_persona);
		$datos = current($datos_persona);

		$titulo = sprintf(_("posibles ca de %s (%s)"),$nom,$ctr);
		$stgr = $datos['stgr'];
		$aActividades = $datos['aActividades'];
	}
	
	$a_campos = ['oPosicion' => $oPosicion,
				'msg_txt' => $msg_txt,
				'titulo' => $titulo,
				'stgr' => $stgr,
				'aActividades' => $aActividades,
				'pagina' => $pagina
				];

	$oView = new core\View('actividadestudios/controller');
	echo $oView->render('ca_posibles_lista.phtml',$a_campos);
} else {
	// -------------------------- si es para el centro/s saco una tabla -------------------------
	foreach($cuadro as $ctr => $datos_persona) {
		
		$a_campos = ['oPosicion' => $oPosicion,
					'msg_txt' => $msg_txt,
					'texto' => $Qtexto,
					'nc_bienio' => $nc_bienio,
					'nc_cuadrienio1' => $nc_cuadrienio1,
					'nc_cuadrienio2' => $nc_cuadrienio2,
					'nc_cuadrienio' => $nc_cuadrienio,
					'nc_repaso' => $nc_repaso,
					'nc_ce' => $nc_ce,
					'nc_otros' => $nc_otros,
					'stgr' => $stgr,
					'ctr' => $ctr,
					'ref' => $Qref,
					'height' => $max_len_activ,
					'cPersonas' => $datos_persona,
					'aActividades' => $aActividades,
					];

		$oView = new core\View('actividadestudios/controller');
		echo $oView->render('ca_posibles_cuadro.phtml',$a_campos);
	}
} 
