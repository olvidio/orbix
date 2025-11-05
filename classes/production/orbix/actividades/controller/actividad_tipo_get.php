<?php

use actividades\model\ActividadLugar;
use actividadtarifas\model\entity\GestorTipoActivTarifa;
use core\ConfigGlobal;
use web\Desplegable;
use web\Lista;
use web\TiposActividades;
use ubis\model\entity\GestorDelegacion;
use function core\is_true;

/**
 * Devuelvo un desplegable con los valores posibles del tipo de actividad
 *  según el valor de entrada.
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
$Qextendida = (string)filter_input(INPUT_POST, 'extendida');
$extendida = (bool)is_true($Qextendida);

switch ($Qsalida) {
    case "asistentes":
        $aux = $Qentrada . '.....';
        $oTipoActiv = new TiposActividades($aux);
        $a_asistentes_posibles = $oTipoActiv->getAsistentesPosibles();
        // la opción en blanco sólo es válida para des o calendario
        if (($_SESSION['oPerm']->have_perm_oficina('des'))
            || ($_SESSION['oPerm']->have_perm_oficina('calendario'))
        ) {
            $blanco = TRUE;
        } else {
            $blanco = FALSE;
        }
        $oDespl = new Desplegable('iasistentes_val', $a_asistentes_posibles, '', $blanco);
        $oDespl->setAction('fnjs_actividad('.$extendida.')');
        $oDespl->setValBlanco('.');
        $oDespl->setOpcion_sel('.');
        echo $oDespl->desplegable();
        break;
    case "actividad":
        $aux = $Qentrada . '....';
        $oTipoActiv = new TiposActividades($aux);
        $a_actividades_posibles = $oTipoActiv->getActividadesPosibles1Digito();
        $opcion_blanco = '.';
        if ($extendida) {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles2Digitos();
            $opcion_blanco = '..';
        }
        $oDespl = new Desplegable('iactividad_val', $a_actividades_posibles, '', true);
        $oDespl->setAction('fnjs_nom_tipo()');
        $oDespl->setValBlanco($opcion_blanco);
        $oDespl->setOpcion_sel($opcion_blanco);
        echo $oDespl->desplegable();
        break;
    case "nom_tipo":
        if ($extendida) {
            $aux = $Qentrada . '..';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();
            $opcion_blanco = '..';
        } else {
            $aux = $Qentrada . '...';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles3Digitos();
            $opcion_blanco = '...';

        }
        $oDespl = new Desplegable('inom_tipo_val', $a_nom_tipo_posibles, '', true);
        $oDespl->setValBlanco($opcion_blanco);
        $oDespl->setOpcion_sel($opcion_blanco);
        if ($Qmodo === 'buscar') {
            $oDespl->setAction('fnjs_id_activ()');
        } else {
            $oDespl->setAction('fnjs_act_id_activ()');
        }
        echo $oDespl->desplegable();
        break;
    case "nom_tipo_tabla":
        $aux = $Qentrada . '..';
        $oTipoActiv = new TiposActividades($aux, TRUE);
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

        $oActividadLugar = new ActividadLugar();
        $oActividadLugar->setIsfsv($Qisfsv);
        $oActividadLugar->setSsfsv($Qssfsv);
        $oActividadLugar->setOpcion_sel($Qopcion_sel);

        $oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qentrada);
        echo $oDesplegableCasas->desplegable();
        break;
    // Si se tiene instalado el modulo de procesos, no se usa, porque se va
    // a la página de nuevo para poder manejar mejor los permisos.
    case 'id_tarifa':
        $id_tipo_activ = $Qentrada;
        $aWhere = [];
        $aWhere['id_tipo_activ'] = $id_tipo_activ;
        $aWhere['_ordre'] = 'id_serie';
        $GesActiTipoTarifa = new GestorTipoActivTarifa();
        $cActiTipoTarifa = $GesActiTipoTarifa->getTipoActivTarifas($aWhere);
        if (!empty($cActiTipoTarifa) && $cActiTipoTarifa > 0) {
            return $cActiTipoTarifa[0]->getId_tarifa();
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
