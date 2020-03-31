<?php
// INICIO Cabecera global de URL de controlador *********************************

use cambios\model\entity\GestorCambioUsuario;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************


$Qque = (string) \filter_input(INPUT_POST, 'que');

switch($Qque) {
    case 'eliminar_fecha':
        $Qf_fin = (string) \filter_input(INPUT_POST, 'f_fin');

        $GesCambioUsuario = new GestorCambioUsuario();
        $GesCambioUsuario->eliminarHastaFecha($Qf_fin);
        
        break;
	case 'eliminar':
	    
	    $a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	    
        foreach($a_sel as $id) {
            $id_item_cmb = strtok($id,'#');
            $id_usuario = strtok('#');
            $sfsv = strtok('#');
            $aviso_tipo = strtok('#');
            $aWhere = ['id_item_cambio'=>$id_item_cmb,
                'id_usuario'=>$id_usuario,
                'sfsv'=>$sfsv,
                'aviso_tipo'=>$aviso_tipo,
            ];
            $GesCambioUsuario = new GestorCambioUsuario();
            $cCambiosUsuario = $GesCambioUsuario->getCambiosUsuario($aWhere);
            foreach($cCambiosUsuario as $oCambioUsuario) {
                if ($oCambioUsuario->DBEliminar() === false) {
                    echo _("Hay un error, no se ha eliminado");
                    echo "\n".$oCambioUsuario->getErrorTxt();
                }
            }
        }
		break;
}
