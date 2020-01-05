<?php
/**
* Esta página muestra una tabla con las actividades que cumplen con la condicion.
* He quitado la posibilidad de buscar opr sacd i por ctr. Quedan las opciones:
*
*@param 	$que
*        	$status por defecto = 2
*        	$id_tipo_activ
*        	$id_ubi
*        	$periodo 
*        	$inicio
*        	$fin 
*        	$year
*        	$dl_org 
*        	$empiezamin por defecto = hoy
* 		 	$empiezamax por defecto = hoy + 6 meses
*
* Si el resultado es más de 200, pregunta si quieres seguir.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		23/8/2007.		
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use ubis\model\entity\Casa;
use web\DateTimeLocal;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_sfsv = ConfigGlobal::mi_sfsv();

$oPosicion->recordar();

$Qcontinuar = (string)  filter_input(INPUT_POST, 'continuar');
// Sólo sirve para esta pagina: importar, publicar, duplicar
$QGstack = (integer)  filter_input(INPUT_POST, 'Gstack');
if (isset($_POST['stack'])) {
    $stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}

//Si vengo de vuelta con el parámetro 'continuar', los datos no están en el POST,
// sino en $Posicion. Le paso la referecia del stack donde está la información.
if (!empty($Qcontinuar) && $Qcontinuar == 'si' && ($QGstack != '')) {
    $oPosicion->goStack($QGstack);
	
    $Qque = $oPosicion->getParametro('que');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi= $oPosicion->getParametro('id_ubi');
    $Qperiodo=$oPosicion->getParametro('periodo');
    $Qyear=$oPosicion->getParametro('year');
    $Qdl_org=$oPosicion->getParametro('dl_org');
    $Qempiezamin=$oPosicion->getParametro('empiezamin');
    $Qempiezamax=$oPosicion->getParametro('empiezamax');

    $Qstatus = $oPosicion->getParametro('status');
	// se usan cuando se viene de lista_activ_sr_que.php y lista_activ_sg_que.php
	// son arrays
	// en este caso status también puede ser un array.
    $Qc_activ = $oPosicion->getParametro('c_activ');
    $Qasist = $oPosicion->getParametro('asist');
    $Qseccion = $oPosicion->getParametro('seccion');
    
    $Qid_sel=$oPosicion->getParametro('id_sel');
    $Qscroll_id = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($QGstack); //limpio todos los estados hacia delante.

} else { //si no vengo por goto.
    $Qid_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string) \filter_input(INPUT_POST, 'scroll_id');
    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel=$oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
    $Qque = (string) \filter_input(INPUT_POST, 'que');
    $Qstatus = (integer) \filter_input(INPUT_POST, 'status');
    $Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
    $Qfiltro_lugar = (string) \filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
    $Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
    $Qyear = (string) \filter_input(INPUT_POST, 'year');
    $Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

    // valores por defecto
    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }
    
	// se usan cuando se viene de lista_activ_sr_que.php y lista_activ_sg_que.php
	// son arrays
	// en este caso status también puede ser un array.
    $Qc_activ = (array)  \filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qasist = (array)  \filter_input(INPUT_POST, 'asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qseccion = (array)  \filter_input(INPUT_POST, 'seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (empty($Qstatus)) {
        $Qa_status = (array)  \filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qstatus = empty($Qa_status)? ActividadAll::STATUS_ACTUAL : $Qa_status;
    }
    
    $aGoBack = [
        'que'=>$Qque,
        'status'=>$Qstatus,
        'id_tipo_activ'=>$Qid_tipo_activ,
        'filtro_lugar'=>$Qfiltro_lugar,
        'id_ubi'=>$Qid_ubi,
        'periodo'=>$Qperiodo,
        'year'=>$Qyear,
        'dl_org'=>$Qdl_org,
        'empiezamin'=>$Qempiezamin,
        'empiezamax'=>$Qempiezamax,
        'c_activ'=>$Qc_activ,
        'asist'=>$Qasist,
        'seccion'=>$Qseccion,
        ];
    $oPosicion->setParametros($aGoBack,1);
}

// Condiciones de búsqueda.
$aWhere = [];
$aOperador = [];
// Status
if (is_array($Qstatus)) {
	$cond_status='';
	foreach ($Qstatus as $status) {
		$cond_status.=$status;	
	}
	$aWhere['status'] = $cond_status;
	$aOperador['status'] = '~';
} elseif (!empty($Qstatus)) {
	$aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	if (($Qque == 'list_activ_inv_sg') OR ($Qque == 'list_activ_sr')){
		$codi_activ_v = [];
		foreach ($Qseccion as $seccion_temp) {
			foreach ($Qasist as $asist_temp) {
				foreach ($Qc_activ as $c_activ_temp) {
					$codi_activ = $seccion_temp.$asist_temp.$c_activ_temp."...";
					//echo "codi_activ: $num_a_grupo.-$codi_activ";
					$codi_activ_v[]=$codi_activ;
				}
			}
		}
		$condicion= implode("|", $codi_activ_v);
		$aWhere['id_tipo_activ'] = "^($condicion)";
		$aOperador['id_tipo_activ'] = '~';
	} else {
	    $Qssfsv = (string) \filter_input(INPUT_POST, 'ssfsv');
	    $Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
	    $Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
	    $Qsnom_tipo = (string) \filter_input(INPUT_POST, 'snom_tipo');
	    
	    if (empty($Qssfsv)) {
	        if ($mi_sfsv == 1) $Qssfsv = 'sv';
	        if ($mi_sfsv == 2) $Qssfsv = 'sf';
	    }
	    $sasistentes = empty($Qsasistentes)? '.' : $Qsasistentes;
	    $sactividad = empty($Qsactividad)? '.' : $Qsactividad;
	    $snom_tipo = empty($Qsnom_tipo)? '...' : $Qsnom_tipo;
		$oTipoActiv= new TiposActividades();
		$oTipoActiv->setSfsvText($Qssfsv);
		$oTipoActiv->setAsistentesText($sasistentes);
		$oTipoActiv->setActividadText($sactividad);
		$id_tipo_activ=$oTipoActiv->getId_tipo_activ();
		if ($id_tipo_activ!='......') {
			$aWhere['id_tipo_activ'] = "^$id_tipo_activ";
			$aOperador['id_tipo_activ'] = '~';
		} 
	}
} else {
	$oTipoActiv= new TiposActividades($Qid_tipo_activ);
	$ssfsv=$oTipoActiv->getSfsvText();
	$sasistentes=$oTipoActiv->getAsistentesText();
	$sactividad=$oTipoActiv->getActividadText();
	if ($Qid_tipo_activ != '......') {
		$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
		$aOperador['id_tipo_activ'] = '~';
	} 
}
// Lugar
if (!empty($Qid_ubi)) {
	$aWhere['id_ubi']=$Qid_ubi;
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
// periodo.
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
    $aOperador['f_fin'] = 'BETWEEN';
} else {
    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
}
// dl Organizadora.
if (!empty($Qdl_org)) {
    $aWhere['dl_org'] = $Qdl_org;
}

//echo"query:$query <br>";
$aWhere['_ordre']='f_ini';
$GesActividades=new GestorActividad();
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

// Titulo	
if (($Qque == 'list_activ_inv_sg') OR ($Qque == 'list_activ_sr')) {
	/*dicho paràmetro le viene del formulario que_lista_activ_sg.php
	o del que_lista_activ_sr*/
	$titulo = (string) \filter_input(INPUT_POST, 'titulo');
	$titulo = ucfirst($titulo);
} else {
	$titulo=ucfirst(_("listado de actividades"));
}
	

// Ver hora si...
if (($Qque=="list_activ_compl")
    OR ($Qque=="list_activ_inv_sg")
    OR ($Qque=="list_activ_sr")
    OR ($_SESSION['oPerm']->have_perm_oficina('vcsd'))
    OR ($_SESSION['oPerm']->have_perm_oficina('des'))) {
	   $ver_hora = 1;
} else {
	$ver_hora = 0;
}
// ver tarifa y sacd si...
if (!(($_SESSION['oPerm']->have_perm_oficina('sg'))
    AND ($Qque=="list_activ_inv_sg") 
    AND !($_SESSION['oPerm']->have_perm_oficina('admin')))) { 
	   $ver_tarifa = 1;
	   $ver_sacd = 1;
} else {
	$ver_tarifa = 0;
	$ver_sacd = 0;
}
$a_cabeceras=array();
if ($Qque=="list_activ_compl") {
	$a_cabeceras[]=ucfirst(_("común"));
}
$a_cabeceras[]= array('name'=>ucfirst(_("empieza")),'class'=>'fecha');

if ($ver_hora == 1) {
	$a_cabeceras[]=ucfirst(_("hora ini"));
}
$a_cabeceras[]= array('name'=>ucfirst(_("termina")),'class'=>'fecha');
if ($ver_hora == 1) {
	$a_cabeceras[]=ucfirst(_("hora fin"));
}
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) { 
	$a_cabeceras[]="sf/sv";
}
$a_cabeceras[]=ucfirst(_("activ."));
$a_cabeceras[]=ucfirst(_("asist."));
$a_cabeceras[]=ucfirst(_("tipo actividad"));
$a_cabeceras[]=ucfirst(_("lugar"));

if ($ver_tarifa == 1) {
	$a_cabeceras[]=ucfirst(_("tar."));
}
$a_cabeceras[]=ucfirst(_("centro"));
if ($ver_sacd == 1) {
	$a_cabeceras[]=ucfirst(_("sacd"));
	$a_cabeceras[]=ucfirst(_("observaciones"));
}
if (ConfigGlobal::is_dmz() === FALSE) {
	$a_cabeceras[]=array('name'=>'','formatter'=>'clickFormatter');
}

$a_botones=array();
$a_valores=array();
$i=0;
foreach ($cActividades as $oActividad) {
	$i++;
	$id_activ = $oActividad->getId_activ();
	$id_tipo_activ = $oActividad->getId_tipo_activ();
	$id_ubi = $oActividad->getId_ubi();
	//$nom_activ = $oActividad->getNom_activ();
	//$dl_org = $oActividad->getDl_org();
	$f_ini = $oActividad->getF_ini()->getFromLocal();
	$f_fin = $oActividad->getF_fin()->getFromLocal();
	$h_ini = $oActividad->getH_ini();
	$h_fin = $oActividad->getH_fin();
	$tarifa = $oActividad->getTarifa();
	$observ = $oActividad->getObserv();
	
	$oUbi = new Casa($id_ubi);
	
	$nombre_ubi = $oUbi->getNombre_ubi();
	if ($oUbi->getSv()=="t") {$comun="sv"; }
	if ($oUbi->getSf()=="t") {$comun="sf"; }
	if (($oUbi->getSv()=="t") and ($oUbi->getSf()=="t")) {$comun="comun"; }

	
	$oTipoActiv= new TiposActividades($id_tipo_activ);
	$ssfsv=$oTipoActiv->getSfsvText();
	$sasistentes=$oTipoActiv->getAsistentesText();
	$sactividad=$oTipoActiv->getActividadText();
	$snom_tipo=$oTipoActiv->getNom_tipoText();

	if ((($_SESSION['oPerm']->have_perm_oficina('sg')) 
	    or ($_SESSION['oPerm']->have_perm_oficina('vcsd')) 
	    or ($_SESSION['oPerm']->have_perm_oficina('des'))) AND !($_SESSION['oPerm']->have_perm_oficina('admin'))) {
		if ($snom_tipo=="(sin especificar)") {	
			$snom_tipo="&nbsp;";
		}
	}

  	if ($Qque=="list_activ_compl") {
		$a_valores[$i][1]=$comun;
	} 
	$a_valores[$i][2]=$f_ini;
	$a_valores[$i][4]=$f_fin;
	if ($ver_hora == 1) {
		if (strlen($h_ini)) {$h_ini=substr($h_ini,0, (strlen($h_ini)-3));}
		if (strlen($h_fin)) {$h_fin=substr($h_fin,0, (strlen($h_fin)-3));}
		$a_valores[$i][3]=$h_ini;
		$a_valores[$i][5]=$h_fin;
	}
	if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) or ($_SESSION['oPerm']->have_perm_oficina('des'))) {
		$a_valores[$i][6]=$ssfsv;
    }
	$a_valores[$i][7]=$sactividad;
	$a_valores[$i][8]=$sasistentes;
	$a_valores[$i][9]=$snom_tipo;
	$a_valores[$i][10]=$nombre_ubi;
	if ($ver_tarifa == 1) {
		$oTarifa = new TipoTarifa($tarifa);
		$tarifa= $oTarifa->getLetra();
		$a_valores[$i][11]=$tarifa;
	}
	$oEnc=new GestorCentroEncargado();
	$ctrs='';
	foreach($oEnc->getCentrosEncargadosActividad($id_activ) as $oEncargado) {;
		$ctrs.=$oEncargado->getNombre_ubi().', ';
	}
	$ctrs=substr($ctrs,0,-2);
	$a_valores[$i][12]=$ctrs;
	if ($ver_sacd == 1) {
		$oCargosActividad=new GestorActividadCargo();
		$sacds='';
		foreach($oCargosActividad->getActividadSacds($id_activ) as $oPersona) {;
			$sacds.=$oPersona->getApellidosNombre()."# "; // la coma la utilizo como separador de apellidos, nombre.
		}
		$sacds=substr($sacds,0,-2);
		$a_valores[$i][13]=$sacds;
		$a_valores[$i][14]=$observ;
	}
	if (ConfigGlobal::is_dmz() === FALSE) {
	    $pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/asistentes/controller/lista_asistentes.php?'."id_pau=$id_activ&que=$Qque");
	    $txt = _("ver asistentes");
	    $a_valores[$i][15]= array( 'ira'=>$pagina, 'valor'=>$txt);
	}
}
// ----------------------------- html -----------------------------------
?>
<?= $oPosicion->mostrar_left_slide(1); ?>
<h3><?= $titulo ?></h3>
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('lista_activ');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
