<?php
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use actividadestudios\model as actividadestudios;
use asistentes\model as asistentes;
use personas\model as personas;
use ubis\model as ubis;
/**
* Esta página tiene la misión de realizar la llamada a calendario php;
* y lo hace con distintos valores, en función de las páginas anteriores
* 
*@param string $tipo planning-> de un grupo de personas n o agd.
*					p_de_paso-> de un grupo de personas de paso.
*					ctr-> de las personas de un ctr.
*					planning_ctr->  de las personas de un ctr.
*					planning_cdc-> actividades que se realizan en una casa del a dl.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
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
switch($_POST['modelo']) {
	case 2:
		$print = 1;
	case 1:
		include_once(core\ConfigGlobal::$dir_estilos.'/calendario.css.php');
		include_once('apps/web/calendario.php');
		break;
	case 3:
		include_once(core\ConfigGlobal::$dir_estilos.'/calendario_grid.css.php');
		include_once('apps/web/calendario_grid.php');
		break;
}

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	// puede ser más de uno
	if (is_array($id_sel) && count($id_sel) > 1) {
		$aid_nom = array();
		foreach ($_POST['sel'] as $nom_sel) {
			$aid_nom[] = $nom_sel;
		}
	} else {
		$aid_nom[] = $id_sel[0];
		$oPosicion->addParametro('id_sel',$id_sel);
		$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
		$oPosicion->addParametro('scroll_id',$scroll_id);
	}
}
	
function actividadesDeUnaCasa($id_ubi,$inicio,$fin) {
	$oInicio = $GLOBALS['oInicio']; 
    $oFin = $GLOBALS['oFin'];
	$a=0;
	$a_cdc = array();
	$aWhere=array();
	$aOperador=array();
	if (empty($id_ubi) || $id_ubi==1) { // en estos casos sólo miro las actividades de cada sección.
		if (empty($id_ubi)) { $aOperador['id_ubi']='IS NULL'; }
		switch ($_POST['cdc_sel']) {
			case 11:
				$aWhere['id_tipo_activ']='^1';
				$aOperador['id_tipo_activ']='~';
				break;
			case 12:
				$aWhere['id_tipo_activ']='^2';
				$aOperador['id_tipo_activ']='~';
				break;
		}
	}
	$aWhere['f_ini']="'$fin'";
	$aOperador['f_ini']='<=';
	$aWhere['f_fin']="'$inicio'";
	$aOperador['f_fin']='>=';
	$aWhere['id_ubi']=$id_ubi;
	$aWhere['status']=4;
	$aOperador['status']='<';
	$oGesActividades = new actividades\GestorActividad();
	$oActividades = $oGesActividades->getActividades($aWhere,$aOperador);
	foreach ($oActividades as $oActividad) {
		extract($oActividad->getTot());

		$oTipoActividad = new web\TiposActividades($id_tipo_activ);
		$ssfsv=$oTipoActividad->getSfsvText();

		//para el caso de que la actividad comience antes
		//del periodo de inicio obligo a que tome una hora de inicio
		//en el entorno de las primeras del día (a efectos del planning
		//ya es suficiente con la 1:16 de la madrugada)  
		$oF_ini=DateTime::createFromFormat('j/n/Y',$f_ini);
		if ($oInicio>$oF_ini) {
			$ini=$inicio;
			$hini="1:16";
		} else {
			$ini=(string) $f_ini;
			$hini=(string) $h_ini;
		}
		$fi= (string) $f_fin;
		$hfi=(string) $h_fin;
				
		// mirar permisos.
		$GLOBALS['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
		$oPermActiv = $GLOBALS['oPermActividades']->getPermisoActual('datos');

		if ($oPermActiv->have_perm('ocupado') === false) { $a++; continue; } // no tiene permisos ni para ver.
		if ($oPermActiv->have_perm('ver') === false) { // sólo puede ver que està ocupado
			$nom_curt= $ssfsv;
			$nom_llarg= "$ssfsv ($ini-$fi)";
		} else {
			$nom_curt=$oTipoActividad->getAsistentesText()." ".$oTipoActividad->getActividadText();
			$nom_llarg=$nom_activ;
		}

		if ($oPermActiv->have_perm('modificar')) { // puede modificar
			// en realidad creo que simplemente tiene que haber algo. Activa la funcion de javascript: cambiar_activ.
			$pagina='programas/actividad_ver.php';
		} else {
			$pagina='';
		}

		$a_cdc[]=array(
					'nom_curt'=>$nom_curt,
					'nom_llarg'=>$nom_llarg,
					'f_ini'=>$ini,
					'h_ini'=>$hini,
					'f_fi'=>$fi,
					'h_fi'=>$hfi,
					'id_tipo_activ'=>$id_tipo_activ,
					'pagina'=>$pagina,
					'id_activ'=>$id_activ
				);
		$a++;
	} 
	// En caso de que todas=0, si no hay actividad, no pongo la casa
	if ($a > 0) {
		return $a_cdc;
	} else {
		return false;
	}
}

$year=empty($_POST['year'])? date('Y')+1 : $_POST['year'];
$_POST['cdc_sel'] = empty($_POST['cdc_sel'])? '' : $_POST['cdc_sel'];

if (empty($_POST['periodo']) || $_POST['periodo'] == 'otro') {
	$inicio = empty($_POST['inicio'])? $_POST['empiezamin'] : $_POST['inicio'];
	$fin = empty($_POST['fin'])? $_POST['empiezamax'] : $_POST['fin'];
} else {
	$oPeriodo = new web\Periodo();
	$oPeriodo->setAny($year);
	$oPeriodo->setPeriodo($_POST['periodo']);
	$inicio = $oPeriodo->getF_ini();
	$fin = $oPeriodo->getF_fin();
}

$oInicio = DateTime::createFromFormat('j/n/Y',$inicio);
$oFin = DateTime::createFromFormat('j/n/Y',$fin);
// valores por defecto.
//divisiones por día
if (empty($_POST['dd']) || (($_POST['dd']<>1) AND ($_POST['dd']<>3))) {
	$_POST['dd']=3;
}
$mod=0; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link. 
$nueva=0; // 0 o 1 para asignar una nueva actividad.
// mostrar encabezados arriba y abajo; derecha e izquierda.
if (empty($print)) { $doble=1; } else { $doble=0; }
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFin->diff($oInicio)->format('%m');
if ($interval < 2) $doble=0;

switch ($_POST['tipo']) {
	case 'planning':
	case 'p_de_paso':
		$cabecera=ucfirst(_('persona seleccionada'));
		$oGesPersonas = new personas\GestorPersonaDl();
		$aWhere['id_nom'] = implode(',',$aid_nom);
		$aOperador['id_nom'] = 'OR';
		$cPersonas = $oGesPersonas->getPersonas($aWhere,$aOperador);
	break;
	case 'ctr':
		if (!empty($_POST['id_ubi'])) { 
			$id_ubi=strtok($_POST['id_ubi'],'#');
			$nombre_ubi=strtok('#');
			$cabecera=ucfirst(sprintf(_('personas de: %s'),$nombre_ubi));
			$GesPersonas = new personas\GestorPersonaDl();
			$aWhereP['id_ctr'] = $id_ubi;
			$cPersonas = $GesPersonas->getPersonasDl($aWhereP);
		}		
	break;
	case 'planning_ctr':
		$aWhere=array();
		$aWhereP = array('situacion'=>'A'); 
		if (empty($_POST['sacd'])) { $aWhereP['sacd']='f'; } 
		if (!empty($_POST['ctr'])) { 
			$nom_ubi = str_replace("+", "\+", $_POST['ctr']); // para los centros de la sss+
			$cabecera=ucfirst(sprintf(_("personas de: %s"),$_POST['ctr']));
			$aWhere['nombre_ubi']=$nom_ubi;
			$aOperador['nombre_ubi']='sin_acentos';
			$GesCentros = new ubis\GestorCentroDl();
			$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
			$cPersonas=''; // para unir todas las personas de más de un centro.
			$GesPersonas = new personas\GestorPersonaDl();
			foreach($cCentros as $oCentro) {
				$id_ubi = $oCentro->getId_ubi();
				$aWhereP['id_ctr'] = $id_ubi;
				$aWhereP['_ordre'] = 'apellido1';
				$cPersonas2 = $GesPersonas->getPersonas($aWhereP);
				if (is_array($cPersonas2) && count($cPersonas2)>=1) {
					if (is_array($cPersonas)) {
						$cPersonas = array_merge($cPersonas,$cPersonas2);
					} else {
						$cPersonas = $cPersonas2;
					}
				}
			}
		} else {
			$cabecera=ucfirst(_('centros'));
			if ((!empty($_POST['todos_n']) && !empty($_POST['todos_agd']))
				|| (empty($_POST['todos_n']) && empty($_POST['todos_agd'])) ) {
			} else {
				if (!empty($_POST['todos_n'])) $aWhereP['id_tabla']='n';
				if (!empty($_POST['todos_agd'])) $aWhereP['id_tabla']='a';
			}
			$aWhereP['_ordre'] = 'id_ctr, apellido1';
			$GesPersonas = new personas\GestorPersonaDl();
			$cPersonas = $GesPersonas->getPersonas($aWhereP);
			$buscar_ctr=1;
			$aListaCtr=array();
		}		
	break;
	case 'planning_cdc':
		$cabecera=ucfirst(_('planning de casas'));
	break;
	case 'casa':
		$cabecera=ucfirst(_('planning de casas'));
		$_POST['status']=array(2);
	break;
}


if ($_POST['tipo']=='planning_cdc' || $_POST['tipo']=='casa') {
	if (!empty($_POST['sin_activ']) && $_POST['sin_activ'] == 1) { $sin_activ = 1; } else { $sin_activ = 0; } //Para dibujar caudricula aunque no tenga actividades.
	if ($_POST['cdc_sel'] < 10) { //Para buscar por casas.
		$aWhere=array();
		$aOperador=array();
		switch ($_POST['cdc_sel']) {
			case 1:
				$aWhere['sv']='t';
				$aWhere['sf']='t';
				break;
			case 2:
				$aWhere['sv']='f';
				$aWhere['sf']='t';
				break;
			case 3: // casas comunes: cdr + dlb + sf +sv
				$aWhere['sv']='t';
				$aWhere['sf']='t';
				$aWhere['tipo_ubi']='cdcdl';
				$aWhere['tipo_casa']='cdc|cdr';
				$aOperador['tipo_casa']='~';
				break;
			case 4:
				$aWhere['sv']='t';
				break;
			case 5:
				$aWhere['sf']='t';
				break;
			case 6:
				$aWhere['sf']='t';
				// también los centros que son como cdc
				$GesCentrosSf = new ubis\GestorCentroSf();
				$cCentrosSf = $GesCentrosSf->getCentrosSf(array('cdc'=>'t','_ordre'=>'nombre_ubi')); 
				break;
			case 9:
				// posible selección múltiple de casas
				if (!empty($_POST['id_cdc'])) {
					$aWhere['id_ubi'] = '^'. implode('$|^',$_POST['id_cdc']) .'$';
					$aOperador['id_ubi'] = '~';
				}
				break;
		}
		$aWhere['_ordre']='nombre_ubi';
		$GesCasaDl = new ubis\GestorCasaDl();
		$cCasasDl = $GesCasaDl->getCasas($aWhere,$aOperador);

		if ($_POST['cdc_sel']==6) { //añado los ctr de sf
			foreach ($cCentrosSf as $oCentroSf) {
				array_push($cCasasDl, $oCentroSf);
			}	
		}

		$p=0;
		$actividades=array();
		foreach ($cCasasDl as $oCasaDl) {
			$a_cdc=array();
			$id_ubi=$oCasaDl->getId_ubi();
			$nombre_ubi=$oCasaDl->getNombre_ubi();

			$cdc[$p]="u#$id_ubi#$nombre_ubi";

			$a_cdc = actividadesDeUnaCasa($id_ubi,$inicio,$fin);
			if ($a_cdc !== false) {
				$actividades[$nombre_ubi]=array($cdc[$p]=>$a_cdc);
				$p++;
			} elseif ($sin_activ == 1) {
				$actividades[$nombre_ubi]=array($cdc[$p]=>array());
				$p++;
			}
		}
		ksort($actividades);
		/*
		lo que sigue es para que nos represente una linea en blanco al final:
		esto permite visualizar correctamente las 3 divisiones en los días 
		en que todas las casas están ocupadas.  
		*/  
		$cdc[$p+1]="##";
		$actividades[]=array($cdc[$p+1]=>array());
	} else { // cdc_sel > 10 Para buscar por actividades (todas).
		// busco todas las actividades del periodo y las agrupo por ubis.
		$oGesActividades = new actividades\GestorActividad();
		$aWhere=array();
		$aOperador=array();
		switch ($_POST['cdc_sel']) {
			case 11:
				$aWhere['id_tipo_activ']='^1';
				$aOperador['id_tipo_activ']='~';
				break;
			case 12:
				$aWhere['id_tipo_activ']='^2';
				$aOperador['id_tipo_activ']='~';
				break;
		}
		$aWhere['f_ini']="'$fin'";
		$aOperador['f_ini']='<=';
		$aWhere['f_fin']="'$inicio'";
		$aOperador['f_fin']='>=';
		$aWhere['status']=4;
		$aOperador['status']='<';
		$aWhere['_ordre']= 'id_ubi';

		$aUbis = $oGesActividades->getUbis($aWhere,$aOperador);
		$p=0;
		$actividades=array();
		foreach ($aUbis as $id_ubi) {
			$a_cdc=array();
			if (empty($id_ubi)) {
				$nombre_ubi= _('por determinar');
				$cdc[$p]="u#2#$nombre_ubi"; // hay que poner un id_ubi para que vaya bien la función de dibujar el calendario.
			} elseif ($id_ubi == 1) {
				$nombre_ubi= _('otros lugares');
				$cdc[$p]="u#$id_ubi#$nombre_ubi";
			} else {
				$oCasa = new ubis\Ubi($id_ubi);
				$id_ubi=$oCasa->getId_ubi();
				$nombre_ubi=$oCasa->getNombre_ubi();
				$cdc[$p]="u#$id_ubi#$nombre_ubi";
			}
			$a_cdc = actividadesDeUnaCasa($id_ubi,$inicio,$fin);
			if ($a_cdc !== false) {
				$actividades[$nombre_ubi]=array($cdc[$p]=>$a_cdc);
				$p++;
			} elseif ($sin_activ == 1) {
				$actividades[$nombre_ubi]=array($cdc[$p]=>array());
				$p++;
			}
		}
		ksort($actividades);
		/*
		lo que sigue es para que nos represente una linea en blanco al final:
		esto permite visualizar correctamente las 3 divisiones en los días 
		en que todas las casas están ocupadas.  
		*/  
		$cdc[$p+1]="##";
		$actividades[]=array($cdc[$p+1]=>array());
	}
} else {
	$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignaturaDl();
	$aWhere = array('f_ini' => "'$inicio','$fin'");
	$aOperador = array('f_ini' => 'BETWEEN');
	$GesActividadAsignaturas->getActividadAsignaturas($aWhere,$aOperador);
	//por cada persona busco las actividades.
	$actividades=array();
	$p=0;
	foreach ($cPersonas as $oPersona) {
		$aActivPersona=array();
		$id_nom=$oPersona->getId_nom();
		$nombre=$oPersona->getApellidosNombre();

		if (!empty($buscar_ctr)) {
			$id_ubi=$oPersona->getId_ctr();
			if (!in_array($id_ubi,$aListaCtr)) {
				$oCentro = new ubis\CentroDl($id_ubi);
				$nombre_ubi = $oCentro->getNombre_ubi();
				$aListaCtr[$id_ubi]=$nombre_ubi;
			} else {
				$nombre_ubi=$aListaCtr[$id_ubi];
			}
			$persona[$p]="p#$id_nom#$nombre#$nombre_ubi";
		} else {
			$persona[$p]="p#$id_nom#$nombre";
		}
		
		$aWhere=array();
		$aWhere['f_ini']="'$fin'";
		$aOperador['f_ini']='<=';
		$aWhere['f_fin']="'$inicio'";
		$aOperador['f_fin']='>=';

		if (core\ConfigGlobal::is_app_installed('actividadcargos')) {
			$oGesActividadCargos = new actividadcargos\GestorActividadCargo();
			$cActividades = $oGesActividadCargos ->getCargoOAsistente($id_nom,$aWhere,$aOperador);
		} else {
			$oGesAsistentes = new asistentes\GestorActividadCargo();
			echo "ja veurem...";
		}
				
		$a=0;
		foreach ($cActividades as $oAsistente) {
			$id_activ = $oAsistente['id_activ'];	
			$propio = $oAsistente['propio'];	

			// Seleccionar sólo las del periodo
			$aWhere['id_activ']=$id_activ;
			$GesActividades = new actividades\GestorActividad();
			$cActividades = $GesActividades->getActividades($aWhere,$aOperador); 
			if (is_array($cActividades) && count($cActividades) == 0) continue;

			$oActividad = $cActividades[0]; // sólo debería haber una.
			extract($oActividad->getTot());

			$oTipoActividad = new web\TiposActividades($id_tipo_activ);
			$ssfsv=$oTipoActividad->getSfsvText();

			//para el caso de que la actividad comience antes
			//del periodo de inicio obligo a que tome una hora de inicio
			//en el entorno de las primeras del día (a efectos del planning
			//ya es suficiente con la 1:16 de la madrugada)  
			$oF_ini=DateTime::createFromFormat('j/n/Y',$f_ini);
			if ($oInicio>$oF_ini) {
				$ini=$inicio;
				$hini="1:16";
			} else {
				$ini=(string) $f_ini;
				$hini=(string) $h_ini;
			}
			$fi= (string) $f_fin;
			$hfi=(string) $h_fin;
				
			// mirar permisos.
			$GLOBALS['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
			$oPermActiv = $GLOBALS['oPermActividades']->getPermisoActual('datos');

			if ($oPermActiv->have_perm('ocupado') === false) continue; // no tiene permisos ni para ver.
			if ($oPermActiv->have_perm('ver') === false) { // sólo puede ver que està ocupado
				$nom_curt= $ssfsv;
				$nom_llarg= "$ssfsv ($ini-$fi)";
			} else {
				$nom_curt=$oTipoActividad->getAsistentesText()." ".$oTipoActividad->getActividadText();
				$nom_llarg=$nom_activ;
			}

			$aActivPersona[]=array(
							'nom_curt'=>$nom_curt,
							'nom_llarg'=>$nom_llarg,
							'f_ini'=>$ini,
							'h_ini'=>$hini,
							'f_fi'=>$fi,
							'h_fi'=>$hfi,
							'id_tipo_activ'=>$id_tipo_activ,
							'pagina'=>'',
							'id_activ'=>$id_activ,
							'propio'=>$propio
						);
			$a++;
		}
		// En los profesores, añado las clases del stgr en actividades
		/*
		$cAsignaturas = $GesActividadAsignaturas->getActividadAsignaturasProfesor($id_nom);
		if ($cAsignaturas !== false) {
			foreach ($cAsignaturas as $oActividadAsignatura) {
				$id_activ = $oActividadAsignatura->getId_activ();
				$oActividad = new actividades\Actividad($id_activ);
				$nom_activ = $oActividad->getNom_activ();

				$f_ini = $oActividadAsignatura->getF_ini();
				$f_fin = $oActividadAsignatura->getF_fin();

				$nom_curt = _('clases stgr');
				$nom_llarg = $nom_curt." "._('en')." ".$nom_activ;
				$aActivPersona[]=array(
								'nom_curt'=>$nom_curt,
								'nom_llarg'=>$nom_llarg,
								'f_ini'=>$f_ini,
								'h_ini'=>'',
								'f_fi'=>$f_fin,
								'h_fi'=>'',
								'id_tipo_activ'=>'',
								'pagina'=>'',
								'id_activ'=>$id_activ,
								'propio'=>''
							);

			}
		}
		*/
		if (!empty($buscar_ctr)) {
			$actividades2[$nombre_ubi][]=array($persona[$p]=>$aActivPersona);
		} else {
			$actividades[]=array($persona[$p]=>$aActivPersona);
		}
		$p++;
	}
}//fin del else

// En el caso de personas doy la opción de volver a los seleccionados.
if ($_POST['tipo']=='planning' || $_POST['tipo']=='p_de_paso' ) {
	echo $oPosicion->atras();
}

// Listo varios centros.
if (!empty($buscar_ctr)) {
	$act=0;
	uksort($actividades2, "strnatcasecmp"); // case insensitive
	foreach( $actividades2 as $nombre_ubi=>$actividades ) {
		$cabecera=$nombre_ubi;
		/*
		lo que sigue es para que nos represente una linea en blanco al final:
		esto permite visualizar correctamente las 3 divisiones en los días 
		en que todas las casas están ocupadas.  
		*/  
		$actividades[]=array('###'=>array());
		$r=dibujar_calendario($_POST['dd'],$cabecera,$inicio,$fin,$actividades,$mod,$nueva,$doble);
		$act++;
	}
} else {
	$r=dibujar_calendario($_POST['dd'],$cabecera,$inicio,$fin,$actividades,$mod,$nueva,$doble);
}
?>
