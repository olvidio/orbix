<?php
/**
* listado de actividades de sr para exportar como csv
* 
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		08/09/20
*		
*/

// Si vengo para descargar, es via GET, por tanto empleo REQUEST

// INICIO Cabecera global de URL de controlador *********************************
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use core\ConfigGlobal;
use ubis\model\entity\Casa;
use usuarios\model\entity\Preferencia;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_sfsv = ConfigGlobal::mi_sfsv();

$Qque = (string) \filter_input(INPUT_POST, 'que');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');

// valores por defecto
if (empty($Qperiodo)) {
    $Qperiodo = 'curso_ca';
}

// son arrays
// en este caso status también puede ser un array.
$Qa_activ = (array)  \filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_status = (array)  \filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_id_cdc = (array)  \filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// json
$json_status = json_encode($Qa_status);
$json_activ = json_encode($Qa_activ);
$json_cdc = json_encode($Qa_id_cdc);
$aPref = [ 'status' => $json_status,
            'periodo' => $Qperiodo,
            'tipo_activ' => $json_activ,
            'ubis_compartidos' => $json_cdc,
        ];

// Guardar Preferencia
//$json_busqueda = "{ 'status': $json_status, 'periodo': '$Qperiodo', 'tipo_activ': $json_activ, 'ubis_compartidos': $json_cdc}";
$json_busqueda = json_encode($aPref);
$id_usuario= core\ConfigGlobal::mi_id_usuario();
$tipo = 'busqueda_activ_sr';
$oPref = new Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
$oPref->setPreferencia($json_busqueda);
if ($oPref->DBGuardar() === false) {
    echo _("hay un error, no se ha guardado la preferencia");
    echo "\n".$oPref->getErrorTxt();
}

// Condiciones de búsqueda.
$aWhere = [];
$aOperador = [];
// Status
if (is_array($Qa_status)) {
	$cond_status='';
	if (count($Qa_status) > 1) {
        foreach ($Qa_status as $status) {
            $cond_status .= $status;	
        }
        $aWhere['status'] = "[$cond_status]";
	} else {
        $aWhere['status'] = $Qa_status[0];
	}
} else {
	$aWhere['status'] = '.';
}
$aOperador['status'] = '~';

// Id tipo actividad
$cv_crt = '';
if (is_array($Qa_activ)) {
    if (count($Qa_activ) > 1) {
        foreach ($Qa_activ as $c_activ) {
            $cv_crt .= $c_activ;
        }
        $cond_act = "[$cv_crt]";
    } else {
        $cond_act = $Qa_activ[0];
    }
} else {
    $cond_act = '.';
}
// sv sr => 17
// sf sr => 27
// sf sr-nax => 28
// sf sr-agd => 29
if ($mi_sfsv == 1) {
    $condicion = '^17'.$cond_act;
} else {
    $condicion = '^2[789]'.$cond_act;
}
$aWhere['id_tipo_activ'] = $condicion;
$aOperador['id_tipo_activ'] = '~';

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
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
}
// dl Organizadora.
if (!empty($Qdl_org)) {
    $aWhere['dl_org'] = $Qdl_org;
}

$aWhere['_ordre']='f_ini';
$GesActividades=new GestorActividad();
$cActividades_1 = $GesActividades->getActividades($aWhere,$aOperador);
// genero un nuevo array con clave el id_activ (como text: precedo 's') para
// poder utilizar array_merge y que me quite los duplicados.
$cActividadesxTipo = [];
foreach ($cActividades_1 as $oActividad) {
    $key = 's'.$oActividad->getId_activ();
    $cActividadesxTipo[$key] = $oActividad;
}

// Añadir ocupación de casas compartidas (con n, sg, agd, etc.)
if (is_array($Qa_id_cdc) && count($Qa_id_cdc) > 0) {
   // borra la condicin del tipo de actividad
   unset($aWhere['id_tipo_activ']);
   unset($aOperador['id_tipo_activ']);
   // añadir la condicion del ubi
   $cond_ubis = "{".implode(', ',$Qa_id_cdc)."}";
   $aWhere['id_ubi'] = $cond_ubis;
   $aOperador['id_ubi'] = 'ANY';
}
$cActividades_2 = $GesActividades->getActividades($aWhere,$aOperador);
// genero un nuevo array con clave el id_activ (como text: precedo 's') para
// poder utilizar array_merge y que me quite los duplicados.
$cActividadesxUbi = [];
foreach ($cActividades_2 as $oActividad) {
    $key = 's'.$oActividad->getId_activ();
    $cActividadesxUbi[$key] = $oActividad;
}


$cActividades = array_merge($cActividadesxTipo, $cActividadesxUbi);


$titulo=ucfirst(_("listado de actividades"));
	
$a_cabeceras=array();
$a_cabeceras[]=ucfirst(_("status"));
$a_cabeceras[]= array('name'=>ucfirst(_("empieza")),'class'=>'fecha');
$a_cabeceras[]= array('name'=>ucfirst(_("termina")),'class'=>'fecha');
$a_cabeceras[]=ucfirst(_("nom activ."));
$a_cabeceras[]=ucfirst(_("asist."));
$a_cabeceras[]=ucfirst(_("tipo actividad"));
$a_cabeceras[]=ucfirst(_("lugar"));
$a_cabeceras[]=ucfirst(_("centro"));

$a_valores=array();
$i=0;
foreach ($cActividades as $oActividad) {
	$i++;
	$id_activ = $oActividad->getId_activ();
	$id_tipo_activ = $oActividad->getId_tipo_activ();
	$status = $oActividad->getStatus();
	$id_ubi = $oActividad->getId_ubi();
	$nom_activ = $oActividad->getNom_activ();
	$f_ini = $oActividad->getF_ini()->getFromLocal();
	$f_fin = $oActividad->getF_fin()->getFromLocal();
	
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
			$snom_tipo="";
		}
	}

	$a_valores[$i][1]=$status;
	$a_valores[$i][2]=$f_ini;
	$a_valores[$i][4]=$f_fin;
	$a_valores[$i][7]=$nom_activ;
	$a_valores[$i][8]=$sasistentes;
	$a_valores[$i][9]=$snom_tipo;
	$a_valores[$i][10]=$nombre_ubi;

	$oEnc=new GestorCentroEncargado();
	$ctrs='';
	foreach($oEnc->getCentrosEncargadosActividad($id_activ) as $oEncargado) {;
		$ctrs.=$oEncargado->getNombre_ubi().', ';
	}
	$ctrs=substr($ctrs,0,-2);
	$a_valores[$i][12]=$ctrs;
	/*
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
	*/
}

$oTabla = new Lista();
$oTabla->setId_tabla('lista_activ');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

if ($Qque == 'file') {
    $filename = 'actividades_sr.csv';
    $oTabla->getCsv($filename);
    die();
}

if ($Qque == 'lista') {
    $oPosicion->recordar();

    $html = $oTabla->mostrar_tabla();
    // ----------------------------- html -----------------------------------
    ?>
    <?= $oPosicion->mostrar_left_slide(1); ?>
    <h3><?= $titulo ?></h3>
    <?php
    echo $html;
}
