<?php
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use permisos\model\PermisosActividades;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\GestorActividadFase;
use ubis\model\entity\GestorCasaDl;
use usuarios\model\entity\Usuario;
use web\Desplegable;
use web\DesplegableArray;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miSfsv = ConfigGlobal::mi_sfsv();

$Qsalida = (string) \filter_input(INPUT_POST, 'salida');

// buscar las fases para estos procesos
switch($Qsalida) {
	case 'guardar':
		$id_cond = $_POST['obj']."_".$_POST['campo']."_cond";
		if (!empty($_POST['id_item'])) {
			$oCambioCampo = new CambioUsuarioCampoPref(array('id_item'=>$_POST['id_item']));
		} else {
			$oCambioCampo = new CambioUsuarioCampoPref();
		}
		
		if (!empty($_POST['campo'])) {
			$oCambioCampo->setCampo($_POST['campo']);
			if ($_POST['campo'] == 'id_ubi') { $_POST['valor'] =implode(",", $_POST['id_ubi']); }
		}
		if (!empty($_POST['operador'])) $oCambioCampo->setOperador($_POST['operador']);
		if (!empty($_POST['valor'])) $oCambioCampo->setValor($_POST['valor']);

		if (!empty($_POST['valor_old'])) $oCambioCampo->setValor_old($_POST['valor_old']);
		if (!empty($_POST['valor_new'])) $oCambioCampo->setValor_new($_POST['valor_new']);
		$cambio_camp = base64_encode(gzcompress(serialize($oCambioCampo))); 
		$condicion = $oCambioCampo->getTextCambio();
		$rta = "<input type='hidden' id=$id_cond name=$id_cond value='$cambio_camp' >$condicion";
		echo $rta;
		break;
	case 'condicion':
		$obj = $_POST['obj'];
		$campo = $_POST['campo'];
		$id_item = $_POST['id_item'];
		
		$a_operadores = array ( "=" => _('igual'),
							"<" => _('menor'),
							">" =>  _('mayor'),
							"regexp" => _('regExp')
							);

		if (!empty($id_item)) {
			$oCambioCampo = new CambioUsuarioCampoPref(array('id_item'=>$id_item));
			
			$valor = $oCambioCampo->getValor();
			$operador = $oCambioCampo->getOperador();
			if (empty($operador)) {
				$chk_old = 'checked';
				$chk_new = 'checked';
			} else {
				$chk_old = ($oCambioCampo->getValor_old()=='t')? 'checked' : '';
				$chk_new = ($oCambioCampo->getValor_new()=='t')? 'checked' : '';
			}
		} else {
			$valor = '';
			$chk_old = 'checked';
			$chk_new = 'checked';
		}
		$txt2 = '<tr>';
		$txt2 .= '<td>'._('avisar si el valor').':';
		$txt2 .= "<input type=\"checkbox\" $chk_new name=\"valor_new\">"._('nuevo');
		$txt2 .= "<input type=\"checkbox\" $chk_old name=\"valor_old\">"._('actual');
		$txt2 .= '</td></tr>';
		$txt2 .= '<tr><td>'._('es').':';
		foreach ($a_operadores as $op => $nom_op) {
			if (empty($operador)) {
				$chk_radio = ($op == '=')? 'checked' : '';
			} else {
				$chk_radio = ($op == $operador)? 'checked' : '';
			}
			$txt2 .= "<input type=\"radio\" $chk_radio name=\"operador\" value=\"$op\">$nom_op";
		}
		$txt2 .= '</td></tr>';
		$txt2 .= '<tr><td>'._('a').':';
		$txt3 = '<input type="input" name="valor" value="'.$valor.'">';
		if ($campo == 'id_ubi') {

			// miro que rol tengo. Si soy casa, sólo veo la mía
			$miRole=$oMiUsuario->getId_role();
			if ($miRole == 9) { //casa
				$id_pau=$oMiUsuario->getId_pau();
				$sDonde=str_replace(",", " OR id_ubi=", $id_pau);
				//formulario para casas cuyo calendario de actividades interesa 
				$donde="WHERE status='t' AND (id_ubi=$sDonde)";
			} else {
				if ($_SESSION['oPerm']->have_perm('des') or $_SESSION['oPerm']->have_perm('vcsd')) {
					$donde="WHERE status='t'";
				} else {
					if ($miSfsv == 1) {
						$donde="WHERE status='t' AND sv='t'";
					}
					if ($miSfsv == 2) {
						$donde="WHERE status='t' AND sf='t'";
					}
				}
			}
			$oGesCasas= new GestorCasaDl();
			$oOpciones = $oGesCasas->getPosiblesCasas($donde);
			
			$oSelects = new DesplegableArray($valor,$oOpciones,'id_ubi');
			//$oSelects->setOpciones($oOpciones);
			$oSelects->setBlanco('t');
			$oSelects->setAccionConjunto('fnjs_mas_casas(event)');
			$txt3="<script>
			fnjs_mas_casas=function(e){
				var code = (e.keyCode ? e.keyCode : e.which);
				if(e==\"x\") {
					var valor=1;
				} else {
					var id_campo='#'+e.currentTarget.id;
					var valor=$(id_campo).val();
					if(code!=9) {
						e.preventDefault();
						e.stopPropagation();
					}
				}
				if ( code==9 || e.type==\"change\" || e==\"x\") {
					if (valor!=0) {";
			$txt3 .= $oSelects->ListaSelectsJs();
			$txt3 .= "}
				}
			}";
			$txt3 .= $oSelects->ComprobarSelectJs(); 
			$txt3 .= '</script>';
			$txt3 .= $oSelects->ListaSelects(); 
		}
		$txt2 .= $txt3;
		$txt2 .= '</td></tr>';

		$txt="<form id='frm_cond'>";
		$txt.='<h3>'.sprintf(_("condición para %s de %s"),$campo,$obj).'</h3>';
		$txt.="<input type=hidden id='salida' name='salida' value=\"update\" > ";
		$txt.="<input type=hidden id='obj' name='obj' value=\"${_POST['obj']}\" > ";
		$txt.="<input type=hidden id='campo' name='campo' value=\"${_POST['campo']}\" > ";
		$txt.="<input type=hidden name=id_item value=\"${_POST['id_item']}\" > ";
		$txt.="<table style=\"width:490\" >";
		$txt.=$txt2;
		$txt.='</table>';
		$txt.='<br><br>';
		$txt.="<input type='button' value='". _('guardar') ."' onclick=\"fnjs_guardar('#frm_cond','guardar');\" >";
		$txt.="<input type='button' value='". _('eliminar') ."' onclick=\"fnjs_guardar('#frm_cond','eliminar');\" >";
		$txt.="<input type='button' value='". _('cancel') ."' onclick=\"fnjs_cerrar();\" >";
		$txt.="</form> ";
		echo $txt;

		break;
	case 'campos':
		include_once('classes/actividades/ext_a_actividades.class');
		include_once('classes/activ-personas/d_cargos_activ.class');
		include_once('classes/activ-ubis/ext_d_encargados_activ.class');

		$id_item_usuario_tabla = $_POST['id_item'];
		$a_item_sel = array();
		$a_campos_sel = array();
		$a_condicion_sel = array();
		if (!empty($id_item_usuario_tabla)) {
			$GesCambiosUsuarioCamposPref = new GestorCambioUsuarioCampoPref();
			$cListaCampos = $GesCambiosUsuarioCamposPref->getCambiosUsuarioCampoPref(array('id_item_usuario_tabla'=>$id_item_usuario_tabla));
			$c = 0;
			foreach ($cListaCampos as $oCambioUsuarioCampoPref) {
				$c++;
				$a_item_sel[$c] = $oCambioUsuarioCampoPref->getId_item();
				$a_campos_sel[$c] = $oCambioUsuarioCampoPref->getCampo();
				$a_condicion_sel[$c] = $oCambioUsuarioCampoPref->getTextCambio();
				$a_cambio_camp_sel[$c] = base64_encode(gzcompress(serialize($oCambioUsuarioCampoPref))); 
			}
		}
		$obj = $_POST['tabla_obj'];
		$oTabla = new $obj;
		$cDatosCampos = $oTabla->getDatosCampos();
		$a_campos = array('todos'=>_('todos'), 'separador'=>'--------');
		$html = "<td></td><td><table><tr><td>";
		$html .="[<span class='link' onclick='fnjs_selectAll(\"#perm_usuario\",\"{$obj}[]\",\"all\");'>"._('todos')."</span>]";
		$html .="  [<span class='link' onclick='fnjs_selectAll(\"#perm_usuario\",\"{$obj}[]\",\"none\");'>"._('ninguno')."</span>]";
		$html .="  [<span class='link' onclick='fnjs_selectAll(\"#perm_usuario\",\"{$obj}[]\",\"toggle\");'>"._('invertir')."</span>]";
		$html .= "</td><td>"._('condición')."</td><td></td></tr>";
		$condicion = _('cualquier cambio');
		$cambio_camp = '';
		foreach ($cDatosCampos as $oDatosCampo) {
			$nom_camp = $oDatosCampo->getNom_camp();
			$etiqueta = $oDatosCampo->getEtiqueta();
			if ($key=array_search($nom_camp,$a_campos_sel)) { 
				$chk_camp = 'checked';
				$condicion = empty($a_condicion_sel[$key])? _('cualquier cambio') : $a_condicion_sel[$key];
				$id_item = $a_item_sel[$key];
				$cambio_camp = $a_cambio_camp_sel[$key];
			} else {
				// para el caso de las casas y los sacd, sólo puede avisar de un cambio suyo.
				// miro que rol tengo. Si soy casa, sólo veo la mía
				$miRole=$oMiUsuario->getId_role();
				if ($nom_camp == 'id_ubi' && $miRole == 9) {
					$id_pau=$oMiUsuario->getId_pau();
					$sDonde=str_replace(",", " OR id_ubi=", $id_pau);

					$chk_camp = 'checked';
					$id_item = '';
					$cambio_camp = '';
					$condicion = _('ja veurem');
				} else {
					$chk_camp = '';
					$id_item = '';
					$cambio_camp = '';
					$condicion = _('cualquier cambio');
				}
			}
			$id = $obj.'_'.$nom_camp;
			$id_cond = $obj.'_'.$nom_camp.'_cond';
			$td_item = $obj.'_'.$nom_camp.'_item';
			$td_cond = "td_$id_cond";
			$txt_mod = "<span class='link' onclick='fnjs_modificar(\"$obj\",\"$nom_camp\",\"$id_item\");'>"._('modificar condición')."</span>";
			$html .= "<tr><td>";
			$html .= "<input type='hidden' id=$td_item name=$td_item value=\"$id_item\" >";
			$html .= "<input type='checkbox' name=\"{$obj}[]\" value=$id_cond $chk_camp >$etiqueta</td>";
			$html .= "<td id=$td_cond><input type='hidden' id=$id_cond name=$id_cond value=\"$cambio_camp\" >$condicion</td>";
			$html .= "<td>$txt_mod</td></tr>";
		}
		$html .= '</table></td>';
		echo $html;
		break;
	case 'desde':
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($_POST['id_tipo_activ'],$_POST['dl_propia']);
		$oGesFases= new GestorActividadFase();
		$oDesplFasesIni = $oGesFases->getListaActividadFases($aTiposDeProcesos);
		$oDesplFasesIni->setNombre('fase_ini');
		echo $oDesplFasesIni->desplegable();
		break;
	case 'hasta':
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($_POST['id_tipo_activ'],$_POST['dl_propia']);
		$oGesFases2= new GestorActividadFase();
		$oDesplFasesFin = $oGesFases2->getListaActividadFases($aTiposDeProcesos);
		$oDesplFasesFin->setNombre('fase_fin');
		echo $oDesplFasesFin->desplegable();
		break;
	case 'av_desde':
		// para las fases cojo los mismos permisos que para las actividasdes (datos).
		$aObjPerm=array('Actividad'=>'datos','ActividadProcesoTarea'=>'datos','ActividadCargoSacd'=>'sacd','ActividadCargo'=>'cargos','ActividadAsistente'=>'asistentes','CentroEncargado'=>'ctr');
		$afecta = $aObjPerm[$_POST['tabla_obj']];
		$id_tipo_activ = (string)"${_POST['id_tipo_activ']}";
		$id_tipo_activ_txt = "......";
		for ($i=0;$i<6;$i++) {
			if (!empty($id_tipo_activ[$i])) $id_tipo_activ_txt[$i] = $id_tipo_activ[$i];
		}
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($_POST['id_tipo_activ'],$_POST['dl_propia']);
		$oGesFases= new GestorActividadFase();
		$aFases = $oGesFases->getArrayActividadFases($aTiposDeProcesos);

		$oPermActividades = new PermisosActividades($_POST['id_usuario']);
		$oPermActividades->setId_tipo_activ($id_tipo_activ_txt);
		$oPermActividades->setPropia($_POST['dl_propia']);
		$aFasesConPerm=array();
		foreach ($aTiposDeProcesos as $id_proceso) {
			$oPermActividades->setId_tipo_proceso($id_proceso);
			foreach ($aFases as $id_fase) {
				//echo "id_fase: $id_fase<br>";
				$oPermActividades->setId_fase($id_fase);
				$oPermActiv = $oPermActividades->getPermisoActual($afecta);
				//print_r($oPermActiv);
				if ($oPermActiv->have_perm('ocupado') === false) continue;
				$oFase = new ActividadFase($id_fase);
				$aFasesConPerm[$id_fase] = $oFase->getDesc_fase();
			}
		}
		$oDesplFasesIni = new Desplegable();
		$oDesplFasesIni->setOpciones($aFasesConPerm);
		$oDesplFasesIni->setNombre('fase_ini');
		echo $oDesplFasesIni->desplegable();
		break;
	case 'av_hasta':
		// para las fases cojo los mismos permisos que para las actividasdes (datos).
		$aObjPerm=array('Actividad'=>'datos','ActividadProcesoTarea'=>'datos','ActividadCargoSacd'=>'sacd','ActividadCargo'=>'cargos','ActividadAsistente'=>'asistentes','CentroEncargado'=>'ctr');
		$afecta = $aObjPerm[$_POST['tabla_obj']];
		$id_tipo_activ = (string)"${_POST['id_tipo_activ']}";
		$id_tipo_activ_txt = "......";
		for ($i=0;$i<6;$i++) {
			if (!empty($id_tipo_activ[$i])) $id_tipo_activ_txt[$i] = $id_tipo_activ[$i];
		}
		// buscar los procesos posibles para estos tipos de actividad
		$GesTiposActiv = new GestorTipoDeActividad();
		$aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($_POST['id_tipo_activ'],$_POST['dl_propia']);
		$oGesFases= new GestorActividadFase();
		$aFases = $oGesFases->getArrayActividadFases($aTiposDeProcesos);

		$oPermActividades = new PermisosActividades($_POST['id_usuario']);
		$oPermActividades->setId_tipo_activ($id_tipo_activ_txt);
		$oPermActividades->setPropia($_POST['dl_propia']);
		$aFasesConPerm=array();
		foreach ($aTiposDeProcesos as $id_proceso) {
			$oPermActividades->setId_tipo_proceso($id_proceso);
			foreach ($aFases as $id_fase) {
				//echo "id_fase: $id_fase<br>";
				$oPermActividades->setId_fase($id_fase);
				$oPermActiv = $oPermActividades->getPermisoActual($afecta);
				//print_r($oPermActiv);
				if ($oPermActiv->have_perm('ocupado') === false) continue;
				$oFase = new ActividadFase($id_fase);
				$aFasesConPerm[$id_fase] = $oFase->getDesc_fase();
			}
		}
		$oDesplFasesFin = new Desplegable();
		$oDesplFasesFin->setOpciones($aFasesConPerm);
		$oDesplFasesFin->setNombre('fase_fin');
		echo $oDesplFasesFin->desplegable();
		break;
}
