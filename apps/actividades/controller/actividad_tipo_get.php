<?php
use ubis\model as ubis;
/*
* Devuelvo un desplegable con los valores posibles segun el valor de entrada.
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

switch ($_POST['salida']) {
	case "tarifa":
		if (!empty($_POST['entrada'])) {
			$id_tipo_activ=$_POST['entrada'];
			$oTipoActivTarifa = new TipoActivTarifa(array('id_tipo_activ'=>$id_tipo_activ));
			echo $oTipoActivTarifa->getTarifa();
		}
	break;
	case "asistentes":
		$aux=$_POST['entrada'].'.....';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_asistentes_posibles =$oTipoActiv->getAsistentesPosibles();
		$oDespl = new web\Desplegable('iasistentes_val',$a_asistentes_posibles,'',true);
	   	$oDespl->setAction('fnjs_actividad()');
		echo $oDespl->desplegable();
	 break;
	case "actividad":
		$aux=$_POST['entrada'].'....';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_actividades_posibles=$oTipoActiv->getActividadesPosibles();
		$oDespl = new web\Desplegable('iactividad_val',$a_actividades_posibles,'',true);
	   	$oDespl->setAction('fnjs_nom_tipo()');
		echo $oDespl->desplegable();
	 break;
	case "nom_tipo":
		$aux=$_POST['entrada'].'...';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_nom_tipo_posibles=$oTipoActiv->getNom_tipoPosibles();
		$oDespl = new web\Desplegable('inom_tipo_val',$a_nom_tipo_posibles,'',true);
	   	$oDespl->setAction('fnjs_act_id_activ()');
		echo $oDespl->desplegable();
	 break;
	 case "lugar":
	 /* Para que los de dre puedan introducir actividades de la sf habrá que hacer de otra manera... */
		$donde='';
		if (empty($_POST['entrada'])) exit;

		$dl_r=strtok($_POST['entrada'],"|");
		$reg=strtok("|");
		switch ($dl_r) {
			case "dl":
				$donde = "WHERE dl='$reg' ";
				$tabla_ctr="u_centros";
				$donde_ctr = "$donde AND cdc='t'";
				break;
			case "r":
				$donde = "WHERE region='$reg' ";
				$tabla_ctr="u_centros";
				$donde_ctr = "$donde AND cdc='t'";
				break;
		}
		$isfsv = empty($_POST['isfsv'])? 0 : $_POST['isfsv'];
		$_POST['ssfsv'] = empty($_POST['ssfsv'])? '' : $_POST['ssfsv'];
		if ($_POST['ssfsv'] == 'sv') $isfsv = 1;
		if ($_POST['ssfsv'] == 'sf') $isfsv = 2;
		switch ($isfsv) {
			case 1:
				$donde_ctr = "$donde AND cdc='t'";
				$donde .= "AND sv='true' ";
				break;
			case 2:
				$donde_ctr = "$donde AND cdc='t'";
				$donde .= "AND sf='true' ";
				break;
		}
		if ($dl_r!="dl" and $dl_r!="r") { $donde=""; }
		if (!empty($donde)) { $donde.=" AND status='t'"; } else { $donde="WHERE status='t'"; }
		$oGesCasas= new ubis\GestorCasa();
		$oOpcionesCasas = $oGesCasas->getPosiblesCasas($donde);
	
		$oGesCentros = new ubis\GestorCentroDl();
		$oOpcionesCentros = $oGesCentros->getPosiblesCentros($donde_ctr);

		$oDesplCasas = new web\Desplegable(array('oOpciones'=>$oOpcionesCasas));	
		$oDesplCasas->setBlanco(true);
		if (!empty($_POST['opcion_sel'])) {
			$oDesplCasas->setOpcion_sel($_POST['opcion_sel']);
		}
		?>
		<select class=contenido id="id_ubi" name="id_ubi">
			<?= $oDesplCasas->options(); ?>
		</select>
		</td>
		<?php
		break;
}
