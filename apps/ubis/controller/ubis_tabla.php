<?php
use ubis\model as ubis;
use usuarios\model as usuarios;
/**
* Esta página muestra una tabla con los ubis seleccionados.
*
* Se tiene en cuenta si es una vuelta de un go_to
*
*@package	delegacion
*@subpackage	ubis
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


$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

//Si vengo de vuelta de un go_to:
if (!empty($_POST['atras'])) {
	$tipo = $oPosicion->getParametro('tipo');
	$loc = $oPosicion->getParametro('loc');
	$sWhere = $oPosicion->getParametro('sWhere');
	$sOperador = $oPosicion->getParametro('sOperador');
	$sGestor = $oPosicion->getParametro('sGestor');
	$sWhereD = $oPosicion->getParametro('sWhereD');
	$sOperadorD = $oPosicion->getParametro('sOperadorD');
	$sGestorDir = $oPosicion->getParametro('sGestorDir');
	$metodo = $oPosicion->getParametro('metodo');
	$titulo = $oPosicion->getParametro('titulo');
} else {
	$loc = empty($_POST['loc'])? '' : $_POST['loc'];
	$tipo = empty($_POST['tipo'])? '' : $_POST['tipo'];
}

/*miro las condiciones. las variables son: nombre_ubi,ciudad,region,pais */
if (empty($sWhere)) {
	$aWhere=array();
	$aOperador=array();
	$aWhereD=array();
	$aOperadorD=array();
	if (!empty($_POST['nombre_ubi'])){
		$nom_ubi = str_replace("+", "\+", $_POST['nombre_ubi']); // para los centros de la sss+
		$aWhere['nombre_ubi']=$nom_ubi;
		$aOperador['nombre_ubi']='sin_acentos';
		//$aWhere['_ordre'] = 'nombre_ubi';
		$aWhere['_ordre'] = 'tipo_ubi,nombre_ubi';
	}
	if (!empty($_POST['region'])){
		$aWhere['region']=$_POST['region'];
		$aWhere['_ordre'] = 'nombre_ubi';
		}
	if (!empty($_POST['dl'])){
		$aWhere['dl']=$_POST['dl'];
		$aOperador['dl']='sin_acentos';
		$aWhere['_ordre'] = 'dl';
		}
	if (!empty($_POST['tipo_ctr'])){
		$aWhere['tipo_ctr']=$_POST['tipo_ctr'];
		$aOperador['tipo_ctr']='sin_acentos';
		$aWhere['_ordre'] = 'tipo_ctr';
		}
	if (!empty($_POST['tipo_casa'])){
		$aWhere['tipo_casa']=$_POST['tipo_casa'];
		$aOperador['tipo_casa']='sin_acentos';
		$aWhere['_ordre'] = 'tipo_casa';
		}


	if (!empty($_POST['ciudad'])){
		$aWhereD['poblacion']=$_POST['ciudad'];
		$aOperadorD['poblacion']='sin_acentos';
		$aWhereD['_ordre']='poblacion';
		}
	if (!empty($_POST['pais'])){
		$aWhereD['pais']=$_POST['pais'];
		$aOperadorD['pais']='sin_acentos';
		$aWhereD['_ordre']='pais';
		}

	//echo "tipo:$tipo<br>";
	//echo "loc:$loc<br>";
	switch ($tipo) {
		case "ctr":
			switch ($loc) {
				case "dl":
					$titulo=ucfirst(_("tabla de centros de la delegación"));
					$Gestor= 'ubis\model\GestorCentroDl';
					$metodo = 'getCentros';
					$GestorDir = 'ubis\model\GestorDireccionCtrDl';
					break;
				case "ex":
					$Gestor= "ubis\model\GestorCentroEx";
					$metodo = 'getCentros';
					$GestorDir = 'ubis\model\GestorDireccionCtrEx';
					$titulo=ucfirst(_("tabla de centros de fuera de la delegación"));
					break;
				case "sf":
					if (($_SESSION['oPerm']->have_perm("vcsd")) OR ($_SESSION['oPerm']->have_perm("des"))) { 
						$Gestor= "ubis\model\GestorCentroDl";
						$Gestor->setoDbl( $GLOBALS['oDBE']);
						$metodo = 'getCentros';
						$GestorDir = 'ubis\model\GestorDireccionCtrDl';
						$titulo=ucfirst(_("tabla de centros de la delegación femenina"));
					}
					break;
				case "tot":
					$Gestor= "ubis\model\GestorCentro";
					$metodo = 'getCentros';
					$titulo=ucfirst(_("tabla de toda las casas y centros"));
					switch ($miSfsv) {
						case 1: // sv
							$aWhere['sv']='t';
							$GestorDir = 'ubis\model\GestorDireccionCtr';
							break;
						case 2: //sf
							$aWhere['sf']='t';
							$GestorDir = 'GestorDireccionSf';
							break;
					}
					break;
			}
			break;
		case "cdc":
			switch ($loc) {
				case "dl":
					$Gestor= "ubis\model\GestorCasaDl";
					$metodo = 'getCasas';
					$titulo=ucfirst(_("tabla de casas de la delegación"));
					$GestorDir = 'ubis\model\GestorDireccionCdcDl'; // Las casas tienen las mismas direcciones que sv.
					break;
				case "ex":
					$Gestor= "ubis\model\GestorCasaEx";
					$metodo = 'getCasas';
					$titulo=ucfirst(_("tabla de casas de fuera de la delegación"));
					$GestorDir = 'ubis\model\GestorDireccionCdcEx';
					break;
				case "sf":
					if (($_SESSION['oPerm']->have_perm("vcsd")) OR ($_SESSION['oPerm']->have_perm("des"))) {
						$Gestor= "ubis\model\GestorCasaDl";
						$metodo = 'getCasas';
						$GestorDir = 'ubis\model\GestorDireccionCdcDl';
						$aWhere['sf']='t';
						$titulo=ucfirst(_("tabla de casas de la sf"));
					}
					break;
				case "tot":
					$Gestor= "ubis\model\GestorCasa";
					$metodo = 'getCasas';
					$titulo=ucfirst(_("tabla de toda las casas y centros"));
					$GestorDir = 'ubis\model\GestorDireccionCdc';
					break;
			}
			break;
		case "tot":
			switch ($loc) {
				case "dl":
					$Gestor= "ubis\model\GestorUbi";
					$metodo = 'getUbis';
					$titulo=ucfirst(_("tabla de casas y centros de la delegación"));
					$GestorDir = 'ubis\model\GestorDireccion';
					break;
				case "ex":
					$Gestor= "ubis\model\GestorUbi";
					$metodo = 'getUbis';
					$titulo=ucfirst(_("tabla de casas y centros de fuera de la delegación"));
					$GestorDir = 'ubis\model\GestorDireccion';
					/*
					switch ($miSfsv) {
						case 1: // sv
							if (($_SESSION['oPerm']->have_perm("vcsd")) OR ($_SESSION['oPerm']->have_perm("des"))) {
								///// FALTA ARREGLAR ESTO /////
								//$cond="(u.dl!='".core\ConfigGlobal::$dele."' OR dl is null)";
								$aWhere['dl']=core\ConfigGlobal::$dele;
								$aWhere['sv']='t';
								$aWhere['tipo_ubi']='ctrsf';
								$aOperador['tipo_ubi']='!=';
							} else {
								$aWhere['dl']=core\ConfigGlobal::$dele;
								$aOperador['dl']='!=';
								$aWhere['sv']='t';
								$aWhere['tipo_ubi']='ctrsf';
								$aOperador['tipo_ubi']='!=';
							}
							break;
						case 2:
							$aWhere['dl']=core\ConfigGlobal::$dele;
							$aOperador['dl']='!=';
							$aWhere['sf']='t';
							break;
					}
					*/
					break;
				case "sf":
					/*
					$Gestor= "ubis\model\GestorUbi";
					$metodo = 'getUbis';
					$titulo=ucfirst(_("tabla de toda las casas y centros"));
					switch ($miSfsv) {
						case 1: // sv
							if (($_SESSION['oPerm']->have_perm("vcsd")) OR ($_SESSION['oPerm']->have_perm("des"))) {
								$aWhere['tipo_ubi']='ctrsf|cdcdl|cdcex';
								$aOperador['tipo_ubi']='~';
								$aWhere['sf']='t';
							}
							break;
						case 2:
							$aWhere['dl']=core\ConfigGlobal::$dele;
							$aOperador['dl']='!=';
							$aWhere['sf']='t';
							break;
					}
					*/
					break;
				case "tot":
					$Gestor= "ubis\model\GestorUbi";
					$metodo = 'getUbis';
					$GestorDir = 'ubis\model\GestorDireccion';
					$titulo=ucfirst(_("tabla de toda las casas y centros"));
					break;
			}
			break;
	}

} else {
	$aWhere = unserialize(core\urlsafe_b64decode($sWhere));
	$aOperador = unserialize(core\urlsafe_b64decode($sOperador));
	$Gestor = unserialize(core\urlsafe_b64decode($sGestor));
	$aWhereD = unserialize(core\urlsafe_b64decode($sWhereD));
	$aOperadorD = unserialize(core\urlsafe_b64decode($sOperadorD));
	$GestorDir = unserialize(core\urlsafe_b64decode($sGestorDir));
	$metodo = $metodo;
}

if (!empty($aWhere)) {
	if (empty($_POST['cmb'])){
		$aWhere['status']='t';
	}
	// En el caso de las casas, hay que distinguir. Lo pongo aqui
	//porque si no hay una condición where anterior, busca todas las casas/centros sf o sv
	switch ($miSfsv) {
		case 1: // sv
			$aWhere['sv']='t';
			break;
		case 2: //sf
			$aWhere['sf']='t';
			break;
	}
	$oUbisGes= new $Gestor;
	$cUbis=$oUbisGes->$metodo($aWhere,$aOperador);
} else {
	$cUbis = array();
}
if (!empty($aWhereD)) {
	$oDireccionesGes = new $GestorDir;
	$cDirecciones = $oDireccionesGes->getDirecciones($aWhereD,$aOperadorD);
	$cUbisD = array();
	foreach($cDirecciones as $oDireccion) {
		$cUbisD = array_merge($cUbisD,$oDireccion->getUbis());
	}
}

// Si hay las dos colleciones, hay que buscar la interseccion.
$aUbisIntersec = array();
if (isset($cUbis) && is_array($cUbis) && count($cUbis) && isset($cUbisD) && is_array($cUbisD) && count($cUbisD)) {
	$aUbis = array();
	foreach ($cUbis as $key => $oUbi) {
		$id_ubi = $oUbi->getId_ubi();
		$aUbis[] = $id_ubi;
	}
	$aUbisD = array();
	foreach ($cUbisD as $key => $oUbi) {
		$id_ubi = $oUbi->getId_ubi();
		$aUbisD[] = $id_ubi;
	}
	foreach ($aUbis as $id_ubi) {
		if (in_array($id_ubi,$aUbisD)) {
			//me lo quedo
			$aUbisIntersec[] = $id_ubi;
		}
	}
} else {
	if (isset($cUbisD) && is_array($cUbisD) && count($cUbisD)) {
		$cUbis = $cUbisD;
	}
}


// para descartar duplicados y ordenar
$aUbis = array();
$cUbisTot = array();
foreach ($cUbis as $key => $oUbi) {
	$id_ubi = $oUbi->getId_ubi();
	if (!empty($aUbisIntersec) && !in_array($id_ubi,$aUbisIntersec)) continue;
	if (in_array($id_ubi,$aUbis)) continue;
	$aUbis[] = $id_ubi;
	$cUbisTot[$key] = $oUbi;
	$region[$key]  = strtolower($oUbi->getRegion());
	$nom[$key]  = strtolower($oUbi->getNombre_ubi());
}

$sWhere = core\urlsafe_b64encode(serialize($aWhere));
$sOperador = core\urlsafe_b64encode(serialize($aOperador));
$sGestor = core\urlsafe_b64encode(serialize($Gestor));
$sWhereD = core\urlsafe_b64encode(serialize($aWhereD));
$sOperadorD = core\urlsafe_b64encode(serialize($aOperadorD));
$sGestorDir = core\urlsafe_b64encode(serialize($GestorDir));

$go_to= '';
//si no existe la ficha, hacer una nueva	
if (is_array($cUbisTot) && count($cUbisTot) == 0) {
	$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/ubis/controller/ubis_buscar.php?'.http_build_query(array('simple'=>1,'tipo'=>$tipo))); 
	$nombre_ubi=urlencode($_POST['nombre_ubi']);
	
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/ubis/controller/ubis_editar.php?'.http_build_query(array('sGestor'=>$sGestor,'nombre_ubi'=>$nombre_ubi,'nuevo'=>1,'go_to'=>$go_to))); 
	
	if ($tipo=="tot" || $loc=="tot") {
		echo _("No existe esta ficha.");
		echo "<br>";
		echo _("OJO!: para crear un centro/casa debe especificar el tipo de centro/casa. Para ello debe buscar a través de 'ver más opciones' definiendo el tipo y la localización distinto a 'todos'.");
	} else {
		printf(_("no existe esta ficha, puede crear una nueva, hacer click <span class=link onclick=fnjs_update_div('#main','%s') > aquí </span>"),$pagina);
	}
	exit;
} else {
	array_multisort($region,SORT_LOCALE_STRING, SORT_ASC,$nom,SORT_LOCALE_STRING, SORT_ASC, $cUbisTot);
}

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'tipo'=>$tipo,
				'loc'=>$loc,
				'sWhere' => $sWhere,
				'sOperador' => $sOperador,
				'sGestor' => $sGestor,
				'sWhereD' => $sWhereD,
				'sOperadorD' => $sOperadorD,
				'sGestorDir' => $sGestorDir,
				'metodo' => $metodo,
				'titulo' => $titulo
				 );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones=array(
				array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(this.form)" )
		);
if ($_SESSION['oPerm']->have_perm("scl")) {
	$a_botones[]=array( 'txt' => _('eliminar'), 'click' =>"fnjs_borrar(this.form)" );
}

$a_cabeceras=array( array('name'=>ucfirst(_("nombre del centro")),'formatter'=>'clickFormatter'),
					_("tipo"),
					_("dl"),
					ucfirst(_("región")),
					ucfirst(_("dirección")),
					_("cp"),
					ucfirst(_("ciudad"))
				);

$a_valores=array();
$i=0;
foreach($cUbisTot as $oUbi) {
	$i++;
	$id_ubi=$oUbi->getId_ubi();
	$tipo_ubi=$oUbi->getTipo_ubi();
	$nombre_ubi=$oUbi->getNombre_ubi();
	$dl=$oUbi->getDl();
	$region=$oUbi->getRegion();

	$cDirecciones = $oUbi->getDirecciones();
	if (is_array($cDirecciones) & !empty($cDirecciones)) {
	    foreach ($cDirecciones as $oDireccion) {
			$poblacion= $oDireccion->getPoblacion();
			$pais= $oDireccion->getPais();
			$direccion= $oDireccion->getDireccion();
			$c_p= $oDireccion->getC_p();
		}
	} else {
		$poblacion= '';
		$pais= '';
		$direccion= '';
		$c_p= '';
	}

	$pagina=web\Hash::link('apps/ubis/controller/home_ubis.php?'.http_build_query(array('pau'=>'u','id_ubi'=>$id_ubi))); 

	$a_valores[$i]['sel']=$id_ubi;
	$a_valores[$i][1]= array( 'ira'=>$pagina, 'valor'=>$nombre_ubi);
	$a_valores[$i][2]=$tipo_ubi;
	$a_valores[$i][3]=$dl;
	$a_valores[$i][4]=$region;
	$a_valores[$i][5]=$direccion;
	$a_valores[$i][6]=$c_p;
	$a_valores[$i][7]=$poblacion;
 }

$oHash = new web\Hash();
$oHash->setcamposForm('!sel');
$a_camposHidden = array(
		'tipo'=>$tipo,
		'loc'=>$loc,
		'go_to'=>$go_to,
		'sWhere'=>$sWhere,
		'sOperador'=>$sOperador,
		'sGestor'=>$sGestor,
		'metodo'=>$metodo,
		'titulo'=>$titulo
		);
$oHash->setArraycamposHidden($a_camposHidden);
// --------------------------------------- html --------------------------------------
?>
<script>
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/ubis/controller/home_ubis.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
fnjs_borrar=function(formulario){
	var seguro;
	seguro=confirm("<?php echo _("¿Está Seguro que desea borrar este ubi?");?>");
	if (seguro) {
		/*$(formulario).attr('action',"programas/ubis_eliminar.php?tabla=<?= $tabla ?>");*/
		$(formulario).submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				complete: function (rta_txt) {
					if (rta_txt != '' && rta_txt != '\n') {
						alert (rta_txt);
					}
				}
			});
			return false;
		});
		$(formulario).submit();
		$(formulario).off();
		/*
		// tacho los marcados
		var form=$(formulario).id;
		// selecciono los elementos con class="sel" de las tablas del id=formulario 
		var sel=$('#'+form+' input.sel');
		$(sel).each(function(i,item){
			if(item.checked== true){
				var s=item.parentNode.parentNode.id;
				$(s).toggleClass('tachado');
			}
		});
		*/
	}
}

</script>
<form id="seleccionados" name="seleccionados" action="programas/ubis_tabla_total.php" method="post">
<?= $oHash->getCamposHtml(); ?>
<h2 class=titulo><?= $titulo; ?>
<input id="tots els camps" name="tots els camps" type=button onclick=fnjs_enviar_formulario('#seleccionados') value="<?= _("listar todos los campos"); ?>"></h2>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
