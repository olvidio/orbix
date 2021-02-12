<?php
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use function core\telecos_persona;
use notas\model as notas;
use ubis\model\entity as ubis;
use web\Hash;
use web\Lista;
use web\Posicion;
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

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$oPosicion->recordar();
	
$Qid_asignatura = (string) \filter_input(INPUT_POST, 'id_asignatura');
$Qpersonas_n = (string) \filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string) \filter_input(INPUT_POST, 'personas_agd');
$Qb_c = (string) \filter_input(INPUT_POST, 'b_c');
$Qc1 = (string) \filter_input(INPUT_POST, 'c1');
$Qc2 = (string) \filter_input(INPUT_POST, 'c2');

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} 

if (empty($Qpersonas_n) && empty($Qpersonas_agd)) {
    exit (_("Debe marcar un grupo de personas (n o agd)"));
}
//miro las condiciones.
if ($Qb_c == 'b'){ 
	$curso="bienio";
	$curso_txt="bienio";
} else {
    // En caso no tener valores, pongo los dos.
    if (empty($Qc1) && empty($Qc2)) {
        $Qc1 = TRUE;
        $Qc2 = TRUE;
    }
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

$oAsignatura = new asignaturas\Asignatura($Qid_asignatura);
$nom_asignatura = $oAsignatura->getNombre_corto();
$id_tipo_asignatura = $oAsignatura->getId_tipo(); // tipo 8 = OPCIONAL

$Pendientes = new notas\AsignaturasPendientes($personas);
$Pendientes->setLista(true);

$aId_nom = $Pendientes->personasQueLesFaltaAsignatura($Qid_asignatura,$curso,$id_tipo_asignatura);

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'b_c'=>$Qb_c,
				'c1'=>$Qc1,
				'c2'=>$Qc2,
				'id_asignatura'=>$Qid_asignatura,
				'personas_n'=>$Qpersonas_n,
				'personas_agd'=>$Qpersonas_agd );
$oPosicion->setParametros($aGoBack,1);

$a_botones=array( array( 'txt' => _("modificar stgr"), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _("ver tessera"), 'click' =>"fnjs_tesera(\"#seleccionados\")" ) 
				);

$a_cabeceras=array( ucfirst(_("tipo")),
                    array('name'=>_("nombre y apellidos"),'formatter'=>'clickFormatter'),
                    ucfirst(_("centro")),
                    ucfirst(_("stgr")),
                    array('name'=>_("telf."),'width'=>80),
                    array('name'=>_("mails"),'width'=>100),
                );

$i = 0;
$a_valores = array();
if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
$obj = 'personas\\model\\entity\\'.$obj_pau;
foreach ($aId_nom as $id_nom=>$aAsignaturas) {
	$i++;
	$oPersona = new $obj($id_nom);
	$id_tabla=$oPersona->getId_tabla();
	$stgr=$oPersona->getStgr();
	$nom=$oPersona->getPrefApellidosNombre();
	// El ctr
	$id_ctr=$oPersona->getId_ctr();
	$oCentroDl = new ubis\CentroDl($id_ctr);
	$nombre_ubi = $oCentroDl->getNombre_ubi();
	
	// Añado los telf:
	$telfs = '';
	$telfs_fijo = telecos_persona($id_nom,"telf","*"," / ",FALSE) ;
	$telfs_movil = telecos_persona($id_nom,"móvil","*"," / ",FALSE) ;
	if (!empty($telfs_fijo) && !empty($telfs_movil)) {
	    $telfs = $telfs_fijo ." / ". $telfs_movil;
	} else {
	    $telfs .= $telfs_fijo??'';
	    $telfs .= $telfs_movil??'';
	}
	$mails = '';
	$mails = telecos_persona($id_nom,"e-mail","*"," / ",FALSE);
	
	$condicion_2="Where id_nom='".$id_nom."'";
	$condicion_2=urlencode($condicion_2);
	$pagina=Hash::link(ConfigGlobal::getWeb().'/apps/personas/controller/home_persona.php?'.http_build_query(array('id_nom'=>$id_nom,'obj_pau'=>$obj_pau)));

	$a_valores[$i]['sel'] = "$id_nom#$id_tabla";
	$a_valores[$i][1] = $id_tabla;
	$a_valores[$i][2] = array( 'ira'=>$pagina, 'valor'=>$nom);
	$a_valores[$i][3] = $nombre_ubi;
	$a_valores[$i][4] = $stgr;
	$a_valores[$i][5] = "$telfs";
	$a_valores[$i][6] = "$mails";
}
if (!empty($a_valores)) {
    if (isset($Qid_sel) && !empty($Qid_sel)) { $a_valores['select'] = $Qid_sel; }
    if (isset($Qscroll_id) && !empty($Qscroll_id)) { $a_valores['scroll_id'] = $Qscroll_id; }
}

$titulo=sprintf(_("lista de %s de %s a los que falta la asignatura %s (%s)"),$gente,$curso_txt,$nom_asignatura,$i);

$oHash = new Hash();
$oHash->setcamposForm('sel!scroll_id');
$a_camposHidden = array(
		'pau' => 'p',
		'obj_pau' => $obj_pau
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
<?= $oPosicion->mostrar_left_slide(1) ?>
<h2 class=titulo><?= $titulo ?></h2>
<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('asig_faltan_personas_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
