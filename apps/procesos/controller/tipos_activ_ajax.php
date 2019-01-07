<?php
use actividades\model\entity\TipoDeActividad;
use procesos\model\entity\GestorProcesoTipo;
use actividades\model\entity\GestorTipoDeActividad;
use web\Lista;
use web\TiposActividades;

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
    case 'lista':
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $oGesTiposDeActividades = new GestorTipoDeActividad();
        $cTiposDeActividades = $oGesTiposDeActividades->getTiposDeActividades($aWhere);
        
		$oGesProcesosTipo = new GestorProcesoTipo();
		$cProcesosTipo = $oGesProcesosTipo->getProcesoTipos();
		$a_procesos_tipo = [];
		foreach ($cProcesosTipo as $oProcesoTipo) {
		    $id_tipo = $oProcesoTipo->getId_tipo_proceso();
		    $nom_proceso = $oProcesoTipo->getNom_proceso();
		    $a_procesos_tipo[$id_tipo] = $nom_proceso;
		}
		
        $a_cabeceras = [];
        $a_cabeceras[]= _("id_tipo_activ");
        $a_cabeceras[]= _("tipo actividad");
        $a_cabeceras[]= _("proceso");
        $a_cabeceras[]= _("proceso no dl");
        
        $a_valores = [];
        $i = 0;
        foreach($cTiposDeActividades as $oTipo) {
            $i++;
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $id_tipo_proceso = $oTipo->getId_tipo_proceso();
            $id_tipo_proceso_ex = $oTipo->getId_tipo_proceso_ex();
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            
            $a_valores[$i][1] = $id_tipo_activ;
            $a_valores[$i][2] = $oTiposActividades->getNom();
            $a_valores[$i][3] = $a_procesos_tipo[$id_tipo_proceso] ?? '?';
            $a_valores[$i][4] = $a_procesos_tipo[$id_tipo_proceso_ex] ?? '?';
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        break;
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
		$txt.="<input type='button' value='". _('cancel') ."' onclick='fnjs_cerrar();' >";
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