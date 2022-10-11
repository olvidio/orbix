<?php

use actividades\model\entity\GestorActividadDl;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\GestorTipoActivTarifa;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use pasarela\model\Conversiones;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorTarifaUbi;
use web\Periodo;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');


$mi_sfsv = ConfigGlobal::mi_sfsv();

$GesCasaDl = new GestorCasaDl();
$aCasasDl = $GesCasaDl->getArrayPosiblesCasas();

$aWhere = [];
$aOperador = [];
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
    $Qssfsv = (string)filter_input(INPUT_POST, 'isfsv_val');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'iasistentes_val');
    $Qsactividad = (string)filter_input(INPUT_POST, 'iactividad_val');

    if (empty($Qssfsv)) {
        if ($mi_sfsv == 1) {
            $Qssfsv = 'sv';
        }
        if ($mi_sfsv == 2) {
            $Qssfsv = 'sf';
        }
    }
    $sasistentes = empty($Qsasistentes) ? '.' : $Qsasistentes;
    $sactividad = empty($Qsactividad) ? '.' : $Qsactividad;
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvId($Qssfsv);
    $oTipoActiv->setAsistentesId($sasistentes);
    $oTipoActiv->setActividadId($sactividad);
    $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
} else {
    $oTipoActiv = new web\TiposActividades($Qid_tipo_activ);
    $ssfsv = $oTipoActiv->getSfsvText();
    $sasistentes = $oTipoActiv->getAsistentesText();
    $sactividad = $oTipoActiv->getActividadText();
}
if ($Qid_tipo_activ !== '......') {
    $aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
    $aOperador['id_tipo_activ'] = '~';
}

$err = '';
// periodo.
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);


$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
$aWhere['f_ini'] = "'$inicioIso','$finIso'";
$aOperador['f_ini'] = 'BETWEEN';

// Casas
$Qaid_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// posible selección múltiple de casas
// una lista de casas (id_ubi).
if (!empty($Qaid_cdc)) {
    $v = "{" . implode(', ', $Qaid_cdc) . "}";
    $aWhere['id_ubi'] = $v;
    $aOperador['id_ubi'] = 'ANY';
}

// Posibles centros encargados
$gesCentrosDl = new GestorCentroDl();
$aCentrosPosibles = $gesCentrosDl->getArrayCentros();
// Quitar el sg o agd del inicio del nombre del ctr
$aCentrosPosiblesSinSgAgd = [];
foreach ($aCentrosPosibles as $id_ubi => $nombre_ubi) {
    $nombre_ubi_sin = preg_replace(array('/^agd/', '/^sg/'), '', $nombre_ubi, 1 );
    $aCentrosPosiblesSinSgAgd[$id_ubi] = $nombre_ubi_sin;
}

$gesActividades = new GestorActividadDl();
$cActividades = $gesActividades->getActividades($aWhere, $aOperador);

$a_cabeceras = [_("activada"),
    _("casa"),
    _("perfil"),
    _("nombre"),
    _("tipo"),
    _("fecha inicio"),
    _("hora inicio"),
    _("fecha fin"),
    _("hora fin"),
    _("activiación"),
    _("plazas max."),
    _("organizador 1"),
    _("organizador 2"),
    _("organizador 3"),
    _("texto aviso"),
    _("contribución obligatoria"),
    _("contribución reserva"),
    _("contribución general"),
    _("contribución estudiante"),
    _("contribución no duerme"),
];

$oConversiones = new Conversiones();

$aConversion_nombre = $oConversiones->getArrayNombre();
$aConversion_tipo = $oConversiones->getArrayTipo();
$aConversion_perfil = $oConversiones->getArrayPerfil();
$aConversion_activacion = $oConversiones->getArrayActivacion();
$contribucion_obligatoria = _("NO");
$aContribucion_reserva =  $oConversiones->getArrayContribucionReserva();
$aTanto_por_cien_contribucion_no_duerme = $oConversiones->getArrayContribucionNoDuerme();

$a_botones = [];
$a_valores = [];
$i = 0;
foreach ($cActividades as $oActividad) {
    $i++;
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $id_activ = $oActividad->getId_activ();
    $id_ubi = $oActividad->getId_ubi();
    $oF_ini = $oActividad->getF_ini();
    $f_ini = $oActividad->getF_ini()->getFromLocal();
    $h_ini = $oActividad->getH_ini;
    $f_fin = $oActividad->getF_fin()->getFromLocal();
    $h_fin = $oActividad->getH_fin;

    $nombre_ubi = empty($aCasasDl[$id_ubi]) ? '??' : $aCasasDl[$id_ubi];
    // plazas
    $plazas_totales = $oActividad->getPlazas();
    if (empty($plazas_totales)) {
        $oCasa = ubis\model\entity\Ubi::NewUbi($id_ubi);
        // A veces por error se puede poner una actividad a un ctr...
        if (method_exists($oCasa, 'getPlazas')) {
            $plazas_max = $oCasa->getPlazas();
            $plazas_min = $oCasa->getPlazas_min();
        }
        $plazas_totales = $plazas_max;
    }
    // centros encargados
    $aWhere = [];
    $aWhere['id_activ'] = $id_activ;
    $aWhere['_ordre'] = 'num_orden DESC';
    $GesCentrosEncargados = new GestorCentroEncargado();
    $cCentrosEncargados = $GesCentrosEncargados->getCentrosEncargados($aWhere);
    $aCentrosEncargados = [0 => '', 1 => '', 2 => ''];
    if (!empty($cCentrosEncargados)) {
        for ($n = 0; $n < 4; $n++) {
            if (!empty($cCentrosEncargados[$n])) {
                $id_ubi = $cCentrosEncargados[$n]->getId_ubi();
                $aCentrosEncargados[$n] = empty($aCentrosPosiblesSinSgAgd[$id_ubi]) ? '?' : $aCentrosPosiblesSinSgAgd[$id_ubi];
            }
        }
    }
    // contribuciones
    $numero_de_dias = $oActividad->getDuracion();
    $id_tarifa = $oActividad->getTarifa();
    if (empty($id_tarifa)) {
        $oGesTipoActivTarifas = new GestorTipoActivTarifa();
        $cTipoActivTarifas = $oGesTipoActivTarifas->getTipoActivTarifas(array('id_tipo_activ' => $id_tipo_activ));
        if (empty($cTipoActivTarifas)) {
            $oTipoActivdad = new TiposActividades($id_tipo_activ);
            $nom_tipo_actividad = $oTipoActivdad->getNom();
            $err .= sprintf(_("No está definido el tipo tarifa para el tipo de actividad: %s"), $nom_tipo_actividad);
            $err .= '<br>';
            $id_tarifa = 0;
        } else {
            $oTipoActivTarifa = $cTipoActivTarifas[0];
            $id_tarifa = $oTipoActivTarifa->getId_tarifa();
        }
    }
    $year = $oF_ini->format('Y');
    $oGesTarifa = new GestorTarifaUbi();
    $cTarifas = $oGesTarifa->getTarifas(['id_ubi' => $id_ubi, 'year' => $year, 'id_tarifa' => $id_tarifa, '_ordre' => 'year,id_tarifa']);
    $cantidad = 0;
    $cantidad_estudiante = 0;
    if (empty($cTarifas)) {
        // buscar el nombre de la tarifa
        $oTipoTarifa = new TipoTarifa($id_tarifa);
        $nombre_tarifa = $oTipoTarifa->getLetra();
        $err .= sprintf(_("No está definida la tarifa %s para la casa %s"), $nombre_tarifa, $nombre_ubi);
        $err .= '<br>';
    } else {
        foreach ($cTarifas as $oTarifa) {
            $id_serie = $oTarifa->getId_serie();
            if ($id_serie === 1) {
                $cantidad = $oTarifa->getCantidad();
            }
            if ($id_serie === 2) {
                $cantidad_estudiante = $oTarifa->getCantidad();
            }
        }
    }

    $contribucion_general = $numero_de_dias * $cantidad;
    $contribucion_estudiante = $numero_de_dias * $cantidad_estudiante;
    if (is_numeric($aTanto_por_cien_contribucion_no_duerme[$id_tipo_activ])) {
        $contribucion_no_duerme = $aTanto_por_cien_contribucion_no_duerme[$id_tipo_activ] / 100 * $contribucion_general;
    } else {
        $contribucion_no_duerme = '?';

    }

    $a_valores[$i][1] = _("Sí");
    $a_valores[$i][2] = $nombre_ubi;
    $a_valores[$i][3] = $aConversion_perfil[$id_tipo_activ];
    $a_valores[$i][4] = $aConversion_nombre[$id_tipo_activ];
    $a_valores[$i][5] = $aConversion_tipo[$id_tipo_activ];
    $a_valores[$i][6] = $f_ini;
    $a_valores[$i][7] = $h_ini;
    $a_valores[$i][8] = $f_fin;
    $a_valores[$i][9] = $h_fin;
    $a_valores[$i][10] = $aConversion_activacion[$id_tipo_activ];
    $a_valores[$i][11] = $plazas_totales;
    $a_valores[$i][12] = $aCentrosEncargados[0];
    $a_valores[$i][13] = $aCentrosEncargados[1];
    $a_valores[$i][14] = $aCentrosEncargados[2];
    $a_valores[$i][15] = ''; //aviso
    $a_valores[$i][16] = $contribucion_obligatoria;
    $a_valores[$i][17] = $aContribucion_reserva[$id_tipo_activ];
    $a_valores[$i][18] = $contribucion_general;
    $a_valores[$i][19] = $contribucion_estudiante;
    $a_valores[$i][20] = $contribucion_no_duerme;
}


$oTabla = new web\Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

echo $err;
echo $oTabla->mostrar_tabla_html();