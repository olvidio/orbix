<?php

use actividadtarifas\model\entity\GestorTipoActivTarifa;
use core\ConfigGlobal;
use ubis\model\entity\GestorDelegacion;
use web\Lista;

/**
 * Devuelvo un desplegable con los valores posibles del tipo de actividad
 *  segun el valor de entrada.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qentrada = (string)filter_input(INPUT_POST, 'entrada');
$Qsalida = (string)filter_input(INPUT_POST, 'salida');
$Qmodo = (string)filter_input(INPUT_POST, 'modo');
$Qmodo = empty($Qmodo) ? 'buscar' : $Qmodo;

switch ($Qsalida) {
    case "asistentes":
        $aux = $Qentrada . '.....';
        $oTipoActiv = new web\TiposActividades($aux);
        $a_asistentes_posibles = $oTipoActiv->getAsistentesPosibles();
        // la opción en blanco sólo es válida para des o calendario
        if (($_SESSION['oPerm']->have_perm_oficina('des'))
            || ($_SESSION['oPerm']->have_perm_oficina('calendario'))
        ) {
            $blanco = TRUE;
        } else {
            $blanco = FALSE;
        }
        $oDespl = new web\Desplegable('iasistentes_val', $a_asistentes_posibles, '', $blanco);
        $oDespl->setAction('fnjs_actividad()');
        $oDespl->setValBlanco('.');
        $oDespl->setOpcion_sel('.');
        echo $oDespl->desplegable();
        break;
    case "actividad":
        $aux = $Qentrada . '....';
        $oTipoActiv = new web\TiposActividades($aux);
        $a_actividades_posibles = $oTipoActiv->getActividadesPosibles1Digito();
        $oDespl = new web\Desplegable('iactividad_val', $a_actividades_posibles, '', true);
        $oDespl->setAction('fnjs_nom_tipo()');
        $oDespl->setValBlanco('.');
        $oDespl->setOpcion_sel('.');
        echo $oDespl->desplegable();
        break;
    case "actividad_extendida":
        $aux = $Qentrada . '....';
        $oTipoActiv = new web\TiposActividades($aux);
        $a_actividades_posibles = $oTipoActiv->getActividadesPosibles2Digitos();
        $oDespl = new web\Desplegable('iactividad_val', $a_actividades_posibles, '', true);
        $oDespl->setAction('fnjs_nom_tipo()');
        $oDespl->setValBlanco('.');
        $oDespl->setOpcion_sel('.');
        echo $oDespl->desplegable();
        break;
    case "nom_tipo":
        $aux = $Qentrada . '...';
        $oTipoActiv = new web\TiposActividades($aux);
        $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles3Digitos();
        $oDespl = new web\Desplegable('inom_tipo_val', $a_nom_tipo_posibles, '', true);
        $oDespl->setValBlanco('...');
        $oDespl->setOpcion_sel('...');
        if ($Qmodo == 'buscar') {
            $oDespl->setAction('fnjs_id_activ()');
        } else {
            $oDespl->setAction('fnjs_act_id_activ()');
        }
        echo $oDespl->desplegable();
        break;
    case "nom_tipo_tabla":
        $aux = $Qentrada . '..';
        $oTipoActiv = new web\TiposActividades($aux, TRUE);
        $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();
        $a_cabeceras = [_("id"), _("nombre")];
        $a_valores = [];
        $i = 0;
        foreach ($a_nom_tipo_posibles as $id => $nom) {
            $i++;
            $a_valores[$i][1] = $id;
            $a_valores[$i][2] = $nom;
        }
        $oTabla = new Lista();
        $oTabla->setBotones('');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);

        echo $oTabla->mostrar_tabla();
        break;
    case "lugar":
        $Qisfsv = (integer)filter_input(INPUT_POST, 'isfsv');
        $Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');
        $Qopcion_sel = (string)filter_input(INPUT_POST, 'opcion_sel');

        $oActividadLugar = new \actividades\model\ActividadLugar();
        $oActividadLugar->setIsfsv($Qisfsv);
        $oActividadLugar->setSsfsv($Qssfsv);
        $oActividadLugar->setOpcion_sel($Qopcion_sel);

        $oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qentrada);
        echo $oDesplegableCasas->desplegable();
        break;
    // Si se tiene instalado el modulo de procesos, no se usa, porque se va
    // a la página de nuevo para poder manejar mejor los permisos.
    case 'tarifa':
        $id_tipo_activ = $Qentrada;
        $aWhere = [];
        $aWhere['id_tipo_activ'] = $id_tipo_activ;
        $aWhere['_ordre'] = 'serie';
        $GesActiTipoTarifa = new GestorTipoActivTarifa();
        $cActiTipoTarifa = $GesActiTipoTarifa->getTipoActivTarifas($aWhere);
        if (!empty($cActiTipoTarifa) && $cActiTipoTarifa > 0) {
            $tarifa = $cActiTipoTarifa[0]->getId_tarifa();
            return $tarifa;
        }
        break;
    case "dl_org";
        $sfsv = $Qentrada;
        $dl_default = ConfigGlobal::mi_delef($sfsv);
        $oGesDl = new GestorDelegacion();
        $oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($sfsv);
        $oDesplDelegacionesOrg->setNombre('dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($dl_default);
        echo $oDesplDelegacionesOrg->desplegable();
        break;
    case "filtro_lugar";
        $sfsv = $Qentrada;
        $dl_default = ConfigGlobal::mi_delef($sfsv);
        $oGesDl = new GestorDelegacion();

        $oDesplFiltroLugar = $oGesDl->getListaDlURegionesFiltro($sfsv);
        $oDesplFiltroLugar->setAction('fnjs_lugar()');
        $oDesplFiltroLugar->setNombre('filtro_lugar');
        echo $oDesplFiltroLugar->desplegable();
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}
