<?php
use actividades\model\entity\TipoDeActividad;
use procesos\model\entity\GestorProcesoTipo;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string) \filter_input(INPUT_POST, 'dl_propia');
$Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');

switch($Qque) {
	case 'editar':
		$oTipo = new TipoDeActividad(array('id_tipo_activ'=>$Qid_tipo_activ));
		$nombre=$oTipo->getNombre();
		switch($Qdl_propia) {
			case 1:
				$id_tipo_proceso=$oTipo->getId_tipo_proceso();
				break;
			case 2:
				$id_tipo_proceso=$oTipo->getId_tipo_proceso_ex();
				break;
		}
		$oLista=new GestorProcesoTipo();
		$oDespl=$oLista->getListaProcesoTipos();
		$oDespl->setNombre('id_tipo_proceso');
		if (!empty($id_tipo_proceso)) $oDespl->setOpcion_sel($id_tipo_proceso);

		$txt=ucfirst(_('descripción'));
		$txt.=":   <input type='text' id='nombre' name='nombre' value='$nombre'>";
		$txt.=ucfirst(_('proceso'));
		$txt.=":   ".$oDespl->desplegable();
		$txt.="<br><input type='button' name='b_guardar' value='"._('guardar')."' onclick='fnjs_guardar();'>";
		echo $txt;
		break;
	case 'update':
        $Qnombre = (string) \filter_input(INPUT_POST, 'nombre');
        
		$oFicha = new TipoDeActividad(array('id_tipo_activ'=>$Qid_tipo_activ));
		$oFicha->DBCarregar();
		$oFicha->setNombre($Qnombre);	
		switch($Qdl_propia) {
			case 1:
				$oFicha->setId_tipo_proceso($Qid_tipo_proceso);	
				break;
			case 2:
				$oFicha->setId_tipo_proceso_ex($Qid_tipo_proceso);	
				break;
		}

		if ($oFicha->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
}