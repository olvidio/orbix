<?php
use actividades\model as actividades;
use personas\model as personas;
use ubis\model as ubis;

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$todos = empty($_POST['todos'])? '' : $_POST['todos'];

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$na=strtok("#"); // id_tabla
	
	$sactividad = empty($_POST['que'])? '' : $_POST['que'];
	$todos = empty($todos)? 1 : $todos;
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
} else { // vengo de actualizar
	$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
	$na = empty($_POST['na'])? '' : $_POST['na'];
	$sactividad = empty($_POST['sactividad'])? '' : $_POST['sactividad'];
	
}

if (($na == 'a' || $na == 'agd') && $sactividad == 'ca') {
	$sactividad = 'cv';
}

$oPersona = new personas\PersonaDl($id_nom);
$ap_nom = $oPersona->getApellidosNombre();

//Miro los actuales
$gesPlazasPeticion = new \actividadplazas\model\GestorPlazaPeticion();
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$id_nom,'tipo'=>$sactividad,'_ordre'=>'orden'));
$sid_activ = '';
foreach ($cPlazasPeticion as $oPlazaPeticion) {
	$id_activ = $oPlazaPeticion->getId_activ();
	$sid_activ .= empty($sid_activ)? $id_activ : ','.$id_activ;
}

// Posibles:
if (!empty($todos) && $todos != 1) {
	$grupo_estudios = $todos;
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
//periodo
switch ($sactividad) {
	case 'ca':
	case 'cv':
		$any=  core\ConfigGlobal::any_final_curs('est');
		$inicurs=core\curso_est("inicio",$any,"est");
		$fincurs=core\curso_est("fin",$any,"est");
		break;
	case 'crt':
		$any=  core\ConfigGlobal::any_final_curs('crt');
		$inicurs=core\curso_est("inicio",$any,"crt");
		$fincurs=core\curso_est("fin",$any,"crt");
		break;
}

$aWhere['f_ini'] = "'$inicurs','$fincurs'";
$aOperador['f_ini'] = 'BETWEEN';
$aWhere['status'] = 2;
$aWhere['_ordre'] = 'f_ini,nivel_stgr';

$cActividades = array();
$sfsv = core\ConfigGlobal::mi_sfsv();
$mi_dele = core\ConfigGlobal::mi_dele();
switch ($na) {
	case "agd":
	case "a":
		//caso de agd
		$id_ctr = empty($_POST['id_ctr_agd'])? '' : $_POST['id_ctr_agd'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$tabla_pau='p_agregados';

		switch ($sactividad) {
			case 'ca': //133
			case 'cv': //133
				$Qid_tipo_activ = '^'.$sfsv.'33';
				break;
			case 'crt':
				$Qid_tipo_activ = '^'.$sfsv.'31';
				break;
		}
		$aWhere['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperador['id_tipo_activ'] = '~';
		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhere,$aOperador);
		// Añado la condición para que no duplique las de midele:
		$aWhere['dl_org'] = $mi_dele;
		$aOperador['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhere,$aOperador);
		
		$cActividades = array_merge($cActividadesDl,array('-------'),$cActividadesPub);
		break;
	case "n":
		// caso de n
		$id_ctr = empty($_POST['id_ctr_n'])? '' : $_POST['id_ctr_n'];
		if ($id_ctr==1) $id_ctr = ''; //es todos los ctr.
		$id_tabla_persona='n';
		$tabla_pau='p_numerarios';
	
		switch ($sactividad) {
			case 'ca': //112
				$Qid_tipo_activ = '^'.$sfsv.'12';
				break;
			case 'crt':
				$Qid_tipo_activ = '^'.$sfsv.'11';
				break;
		}
		$aWhere['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperador['id_tipo_activ'] = '~';
		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhere,$aOperador);
		// Añado la condición para que no duplique las de midele:
		$aWhere['dl_org'] = $mi_dele;
		$aOperador['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhere,$aOperador);
		
		$cActividades = array_merge($cActividadesDl,array('-------'),$cActividadesPub);
	break;
}

$aOpciones = array();
foreach ($cActividades as $oActividad) {
	// para el separador '-------'
	if (is_object($oActividad)) {
		$id_activ = $oActividad->getId_activ();
		$nom_activ = $oActividad->getNom_activ();
		$aOpciones[$id_activ] = $nom_activ;
	} else {
		$aOpciones[1] = '--------';
	}
}

$oSelects = new web\DesplegableArray($sid_activ,$aOpciones,'actividades');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_actividades(event)');

$oHash = new web\Hash();
$camposForm = 'actividades!actividades_mas!actividades_num';
$oHash->setcamposForm($camposForm);
$oHash->setcamposNo('que!actividades');
$a_camposHidden = array(
		'id_nom' => $id_nom,
		'na' => $na,
		'sactividad' => $sactividad,
		'que' => ''
		);
$oHash->setArraycamposHidden($a_camposHidden);

$txt_guardar=_("guardar peticiones");

echo $oPosicion->mostrar_left_slide();
?>
<script>
fnjs_mas_actividades=function(evt){
	if(evt=="x") {
		var valor=1;
	} else {
		var id_campo=evt.currentTarget.id;
		var valor=$(id_campo).val();
		evt.preventDefault();
		evt.stopPropagation();
	}
	if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
		if (valor!=0) {
			<?php
				echo $oSelects->ListaSelectsJs();
			?>
		} else {
			//ir_a('f_entrada');
		}
	}
}
fnjs_guardar=function(formulario){
	$('#que').val('update');
	$(formulario).attr('action',"apps/actividadplazas/controller/peticiones_activ_ajax.php");
	fnjs_enviar_formulario(formulario);
}
fnjs_borrar=function(formulario){
	$('#que').val('borrar');
	$(formulario).attr('action',"apps/actividadplazas/controller/peticiones_activ_ajax.php");
	$(formulario).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			type: 'post',
			url: $(this).attr('action'),
			complete: function (rta) { 
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				}
			},
			success: function() { fnjs_actualizar() }
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}

fnjs_actualizar=function(){
	$('#frm_peticiones').attr('action','apps/actividadplazas/controller/peticiones_activ.php');
	fnjs_enviar_formulario('#frm_peticiones');
}

<?php
echo $oSelects->ComprobarSelectJs();
?>
</script>
<h3><?= $ap_nom ?></h3>
<form id=frm_peticiones  name=frm_peticiones action='' method="post" >
<?= $oHash->getCamposHtml(); ?>
<table>
	<tr>
	<td class=etiqueta width="30%"><?php echo _("actividades"); ?>:</td>	
	<td id="col_actividades"> <?= $oSelects->ListaSelects(); ?></td></tr>
</table>
	<input type=button onclick="fnjs_guardar(this.form);" value="<?= $txt_guardar ?>">
	<input type=button onclick="fnjs_borrar(this.form);" value="<?= _("borrar") ?>">
</form>