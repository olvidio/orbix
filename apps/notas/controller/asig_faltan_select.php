<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;
use ubis\model as ubis;
/**
* Esta página muestra una tabla con las personas que cumplen con la condicion.
*
* Es llamado desde personas_que.php
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//si vengo por un goto:
if (!empty($_POST['atras'])) {
	$Qnumero = $oPosicion->getParametro('numero');
	$Qb_c = $oPosicion->getParametro('b_c');
	$Qc1 = $oPosicion->getParametro('c1');
	$Qc2 = $oPosicion->getParametro('c2');
	$Qlista = $oPosicion->getParametro('lista');
	$Qpersonas_n = $oPosicion->getParametro('personas_n');
	$Qpersonas_agd = $oPosicion->getParametro('personas_agd');
	$Qtitulo = $oPosicion->getParametro('titulo');
} else { //si no vengo por goto.
	$Qnumero = empty($_POST['numero'])? '' : $_POST['numero'];
	$Qb_c = empty($_POST['b_c'])? '' : $_POST['b_c'];
	$Qc1 = empty($_POST['c1'])? '' : $_POST['c1'];
	$Qc2 = empty($_POST['c2'])? '' : $_POST['c2'];
	$Qlista = empty($_POST['lista'])? false : true;
	$Qpersonas_n = empty($_POST['personas_n'])? '' : $_POST['personas_n'];
	$Qpersonas_agd = empty($_POST['personas_agd'])? '' : $_POST['personas_agd'];
	$Qtitulo = '';
}

//miro las condiciones.
if ($Qb_c == 'b'){ 
	$curso="bienio";
	$curso_txt="bienio";
} else {
	if ($Qc1 && $Qc2) {
		$curso="cuadrienio";
		$curso_txt="cuadrienio";
	} elseif (!empty($Qc2)) {
		$curso="c2";
		$curso_txt="cuadrienio años II-IV";
	} elseif (!empty($Qc1)) {
		$curso="c1";
		$curso_txt="cuadrienio año I";
	}
}
if (!empty($Qpersonas_n)) {
   	$personas="p_numerarios";
   	$gente="numerarios";
	$obj_pau = 'PersonaN';
}
if (!empty($Qpersonas_agd)) {
	$personas="p_agregados";
	$gente="agregados";
	$obj_pau = 'PersonaAgd';
}
if (!empty($Qpersonas_n) && !empty($Qpersonas_agd)) {
	$personas="personas_dl";
	$gente="numerarios y agregados";
	$obj_pau = 'PersonaDl';
}

$Pendientes = new notas\AsignaturasPendientes($personas);
$Pendientes->setLista($Qlista);
$aId_nom = $Pendientes->personasQueLesFalta($Qnumero,$curso);

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'titulo'=>$Qtitulo,
				'numero'=>$Qnumero,
				'b_c'=>$Qb_c,
				'c1'=>$Qc1,
				'c2'=>$Qc2,
				'lista'=>$Qlista,
				'personas_n'=>$Qpersonas_n,
				'personas_agd'=>$Qpersonas_agd );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones=array( array( 'txt' => _('modificar stgr'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('ver tesera'), 'click' =>"fnjs_tesera(\"#seleccionados\")" ) 
				);

$a_cabeceras=array( ucfirst(_("tipo")), array('name'=>_("nombre y apellidos"),'formatter'=>'clickFormatter'), ucfirst(_("centro")), ucfirst(_("stgr")), ucfirst(_("asignaturas")) );

if (empty($titulo)) {
	$titulo=sprintf(_("Lista de %s a los que faltan %d o menos asignaturas para finalizar el %s"),$gente,$Qnumero,$curso_txt);
} else {
	$titulo=urldecode($titulo);
}
		
if (empty($breve)) $breve="";
if (empty($es_sacd)) $es_sacd="";
$i=0;
$a_valores=array();
$obj = 'personas\\model\\'.$obj_pau;
foreach ($aId_nom as $id_nom=>$aAsignaturas) {
	$i++;
	$oPersona = new $obj($id_nom);
	$id_tabla=$oPersona->getId_tabla();
	$stgr=$oPersona->getStgr();
	$nom=$oPersona->getApellidosNombre();
	// El ctr
	$id_ctr=$oPersona->getId_ctr();
	$oCentroDl = new ubis\CentroDl($id_ctr);
	$nombre_ubi = $oCentroDl->getNombre_ubi();

	$condicion_2="Where id_nom='".$id_nom."'";
	$condicion_2=urlencode($condicion_2);
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$obj_pau,'breve'=>$breve,'es_sacd'=>$es_sacd)));

	if ($Qlista == true) { //Hacer un listado de las asignaturas que le faltan
		$as = '';
		foreach ($aAsignaturas as $asig) {
			$as .= empty($as)? '' : "\n";
			$as .= $asig;
		}
	} else {
		$as=$aAsignaturas; 
	}
	$a_valores[$i]['sel'] = "$id_nom#$id_tabla";
	$a_valores[$i][1] = $id_tabla;
	$a_valores[$i][2] = array( 'ira'=>$pagina, 'valor'=>$nom);
	$a_valores[$i][3] = $nombre_ubi;
	$a_valores[$i][4] = $stgr;
	$a_valores[$i][5]=$as;
}


$oHash = new web\Hash();
$oHash->setcamposForm('sel!scroll_id');
$a_camposHidden = array(
		'pau' => 'p',
		'obj_pau' => $obj_pau,
		'breve' => $breve,
		'es_sacd' => $es_sacd
		);
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<script>
fnjs_tesera=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/notas/controller/tessera_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}

fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/personas/controller/stgr_cambio.php");
  		fnjs_enviar_formulario(formulario);
  	}
}

</script>
<h2 class=titulo><?= $titulo ?></h2>
<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('asig_faltan_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
