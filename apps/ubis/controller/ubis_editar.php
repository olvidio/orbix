<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use ubis\model\entity\CasaDl;
use ubis\model\entity\CentroDl;
use ubis\model\entity\GestorDelegacion;
use ubis\model\entity\GestorRegion;
use web\Desplegable;
use web\Hash;
use function core\is_true;

/**
 * Es el frame inferior. Muestra la ficha de los ubis
 *
 * Se incluye la página ficha.php que contiene la función ficha.
 * Esta página sirve para definir los parámetros que se le pasan a la función ficha.
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');

$es_de_dl = FALSE;
if (!empty($Qnuevo)) {
    $tipo_ubi = (string)filter_input(INPUT_POST, 'tipo_ubi');
    $QsGestor = (string)filter_input(INPUT_POST, 'sGestor');
    $Gestor = unserialize(core\urlsafe_b64decode($QsGestor), ['allowed_classes' => false]);
    $obj = str_replace('Gestor', '', $Gestor);
    $oUbi = new $obj();
    $Qobj_pau = str_replace('ubis\\model\\entity\\', '', $obj);

    $cDatosCampo = $oUbi->getDatosCampos();
    $oDbl = $oUbi->getoDbl();
    foreach ($cDatosCampo as $oDatosCampo) {
        $camp = $oDatosCampo->getNom_camp();
        $valor_predeterminado = $oDatosCampo->datos_campo($oDbl, 'valor');
        $a_campos[$camp] = $valor_predeterminado;
    }
    $dl = (string)filter_input(INPUT_POST, 'dl');
    $region = (string)filter_input(INPUT_POST, 'region');
    $nombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');

    if (empty($dl) && strstr($Qobj_pau, 'Dl')) {
        if (strstr($tipo_ubi, 'ctr')) {
            $dl = ConfigGlobal::mi_delef();
        }
        if (strstr($tipo_ubi, 'cdc')) {
            $dl = ConfigGlobal::mi_dele();
        }
    }

    if (empty($region) && strstr($Qobj_pau, 'Dl')) {
        $region = ConfigGlobal::mi_region();
    }

    $nombre_ubi = urldecode($nombre_ubi);
    $oUbi->setNombre_ubi($nombre_ubi);
    $oUbi->setDl($dl);
    $oUbi->setRegion($region);
    $oUbi->setTipo_ubi($tipo_ubi);
    $oUbi->setStatus(true);

    if (strstr($tipo_ubi, 'cdc')) {
        if (ConfigGlobal::mi_sfsv() == 1) {
            $oUbi->setSv(TRUE);
        }
        if (ConfigGlobal::mi_sfsv() == 2) {
            $oUbi->setSf(TRUE);
        }
    }

    $Qid_ubi = '';
    $id_direccion = '';
    $status = true;
} else {
    $obj = 'ubis\\model\\entity\\' . $Qobj_pau;
    $oUbi = new $obj($Qid_ubi);

    $tipo_ubi = $oUbi->getTipo_ubi();
    $dl = $oUbi->getDl();
    $id_ubi = $oUbi->getId_ubi();
    $region = $oUbi->getRegion();
    $nombre_ubi = $oUbi->getNombre_ubi();
    $status = $oUbi->getStatus();
    $id_direccion = '';

    // para saber si es de la dl o no, diferente para ctr o cdc.
    if (strstr($tipo_ubi, 'ctr')) {
        if ($dl == ConfigGlobal::mi_delef()) {
            $es_de_dl = TRUE;
        } else {
            // Aunque el tipo sea ctrdl, si es diferente a la mia, lo trato como ctrex.
            $tipo_ubi = 'ctrex';
        }
    }
    if (strstr($tipo_ubi, 'cdc')) {
        if ($dl == ConfigGlobal::mi_dele()) {
            $es_de_dl = TRUE;
        } else {
            // Aunque el tipo sea cdcdl, si es diferente a la mia, lo trato como cdcex.
            $tipo_ubi = 'cdcex';
        }
    }
    // si es de la dl, poner que obj_pau sea dl:
    if ($es_de_dl) {
        if ($tipo_ubi == 'ctrdl') {
            $oUbi_new = new CentroDl($id_ubi);
            // comprobar que realmente es el mismo:
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                $Qobj_pau = 'CentroDl';
            }
        }
        if ($tipo_ubi == 'cdcdl') {
            $oUbi_new = new CasaDl($id_ubi);
            // comprobar que realmente es el mismo:
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                $Qobj_pau = 'CasaDl';
            }
        }
    }
}

$isfsv = ConfigGlobal::mi_sfsv();
// Para incluir o no la propia dl
$Bdl = 't';
$oGesDl = new GestorDelegacion();
// si es ctr, dlbf, si es casa dlb
if ($Qobj_pau == 'CasaDl' || $Qobj_pau == 'Casa' || $Qobj_pau == 'CasaEx') {
    $oDesplDelegaciones = $oGesDl->getListaDelegacionesURegiones(1, $Bdl);
} else {
    $oDesplDelegaciones = $oGesDl->getListaDelegacionesURegiones($isfsv, $Bdl);
}
$oDesplDelegaciones->setNombre('dl');

$gesReiones = new GestorRegion();
$oDesplRegiones = $gesReiones->getListaRegiones();
$oDesplRegiones->setNombre('region');

//----------------------------------Permisos según el usuario
$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
4: quitar direccion
*/
if (strstr($Qobj_pau, 'Dl')) {
    if (!empty($Qnuevo) || $es_de_dl) {
        // ----- sólo a scl -----------------
        if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
            $botones = "1,2";
        }
    }
} else if (strstr($Qobj_pau, 'Ex')) {
    // ----- sólo a scl -----------------
    if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
        $botones = "1,2";
    }
}

$oPermActiv = new ubis\model\CuadrosLabor();

$chk = is_true($status) ? 'checked' : '';
$campos_chk = 'status!sv!sf';

$camposForm = 'que!dl!tipo_ubi!status!region!nombre_ubi';
if ($tipo_ubi === "ctrdl" || $tipo_ubi === "ctrsf") {
    $camposForm .= '!num_pi!num_cartas!num_cartas_mensuales!plazas!num_habit_indiv!n_buzon!observ';
}
if ($tipo_ubi === "ctrdl" || $tipo_ubi === "ctrex" || $tipo_ubi === "ctrsf") {
    $camposForm .= '!id_ctr_padre!tipo_ctr';
    $campos_chk .= '!cdc!tipo_labor';
}
if ($tipo_ubi === "cdcdl" || $tipo_ubi === "cdcex") {
    $camposForm .= '!tipo_casa!plazas!plazas_min!num_sacd!sf!sv';
}
$oHash = new Hash();
$oHash->setcamposNo('que!' . $campos_chk);
$oHash->setCamposForm($camposForm);
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    'id_direccion' => $id_direccion
);
$oHash->setArraycamposHidden($a_camposHidden);


$oView = new ViewPhtml('ubis\controller');

switch ($tipo_ubi) {
    case "ctrdl":
    case "ctrsf":
        $cdc = $oUbi->getCdc();
        $chk_cdc = is_true($cdc)? 'checked' : '';
        $tipo_labor = $oUbi->getTipo_labor();
        $id_ctr_padre = $oUbi->getId_ctr_padre();
        $tipo_ctr = $oUbi->getTipo_ctr();
        $num_pi = $oUbi->getNum_pi();
        $num_cartas = $oUbi->getNum_cartas();
        $num_cartas_mensuales = $oUbi->getNum_cartas_mensuales();
        $num_habit_indiv = $oUbi->getNum_habit_indiv();
        $plazas = $oUbi->getPlazas();
        $n_buzon = $oUbi->getN_buzon();
        $observ = $oUbi->getObserv();

        $dl = empty($dl) ? ConfigGlobal::mi_delef() : $dl;
        $region = empty($region) ? ConfigGlobal::mi_region() : $region;

        $GesCentro = new ubis\model\entity\GestorCentro();
        if (!empty($dl)) {
            $sWhere = "WHERE dl = '$dl'";
        } else if (!empty($region)) {
            $sWhere = "WHERE region = '$region'"; //probar con la region
        } else {
            $sWhere = ''; // Hay muchos ctr que no tienen puesta la dl.
        }
        $oDesplCentros = $GesCentro->getListaCentros($sWhere);
        $nnom = "id_ctr_padre";
        $oDesplCentros->setNombre($nnom);
        $oDesplCentros->setOpcion_sel($id_ctr_padre);

        $oTiposCentro = new ubis\model\entity\GestorTipoCentro();
        $oTiposCentroOpciones = $oTiposCentro->getListaTiposCentro();
        $oDesplegableTiposCentro = new Desplegable('tipo_ctr', $oTiposCentroOpciones, $tipo_ctr, true);

        $oDesplDelegaciones->setOpcion_sel($dl);
        $oDesplRegiones->setOpcion_sel($region);
        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $tipo_ctr,
            'num_pi' => $num_pi,
            'num_cartas' => $num_cartas,
            'num_cartas_mensuales' => $num_cartas_mensuales,
            'oPermActiv' => $oPermActiv,
            'tipo_labor' => $tipo_labor,
            'num_habit_indiv' => $num_habit_indiv,
            'plazas' => $plazas,
            'n_buzon' => $n_buzon,
            'observ' => $observ,
            'chk_cdc' => $chk_cdc,
            'oDesplCentros' => $oDesplCentros,
            'oDesplegableTiposCentro' => $oDesplegableTiposCentro,
            'oDesplDelegaciones' => $oDesplDelegaciones,
            'oDesplRegiones' => $oDesplRegiones,
        ];

        $oView->renderizar('ctrdl_form.phtml', $a_campos);
        break;
    case "ctrex":
        $cdc = $oUbi->getCdc();
        $chk_cdc = is_true($cdc)? 'checked' : '';
        $tipo_labor = $oUbi->getTipo_labor();
        $id_ctr_padre = $oUbi->getId_ctr_padre();
        $tipo_ctr = $oUbi->getTipo_ctr();

        $GesCentro = new ubis\model\entity\GestorCentro();
        if (!empty($dl)) {
            $sWhere = "WHERE dl = '$dl'";
        } else if (!empty($region)) {
            $sWhere = "WHERE region = '$region'"; //probar con la region
        } else {
            $sWhere = ''; // Hay muchos ctr que no tienen puesta la dl.
        }
        $oDesplCentros = $GesCentro->getListaCentros($sWhere);
        $nnom = "id_ctr_padre";
        $oDesplCentros->setNombre($nnom);
        $oDesplCentros->setOpcion_sel($id_ctr_padre);

        $oTiposCentro = new ubis\model\entity\GestorTipoCentro();
        $oTiposCentroOpciones = $oTiposCentro->getListaTiposCentro();
        $oDesplegableTiposCentro = new Desplegable('tipo_ctr', $oTiposCentroOpciones, $tipo_ctr, true);

        $oDesplDelegaciones->setOpcion_sel($dl);
        $oDesplRegiones->setOpcion_sel($region);
        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $tipo_ctr,
            'chk_cdc' => $chk_cdc,
            'oDesplCentros' => $oDesplCentros,
            'tipo_labor' => $tipo_labor,
            'oPermActiv' => $oPermActiv,
            'oDesplegableTiposCentro' => $oDesplegableTiposCentro,
            'oDesplDelegaciones' => $oDesplDelegaciones,
            'oDesplRegiones' => $oDesplRegiones,
        ];

        $oView->renderizar('ctrex_form.phtml', $a_campos);
        break;
    case "cdcdl":
    case "cdcex":
        // OJO LAS CASAS pueden ser comunes. la dl es sin 'f'.
        if ($tipo_ubi == "cdcdl") {
            $dl = empty($dl) ? ConfigGlobal::mi_dele() : $dl;
            $region = empty($region) ? ConfigGlobal::mi_region() : $region;
        }

        $tipo_casa = $oUbi->getTipo_casa();
        $plazas = $oUbi->getPlazas();
        $plazas_min = $oUbi->getPlazas_min();
        $num_sacd = $oUbi->getNum_sacd();
        $sv = $oUbi->getSv();
        $sf = $oUbi->getSf();

        $sv_chk = is_true($sv)? 'checked' : '';
        $sf_chk = is_true($sf)? 'checked' : '';
        $oTiposCasa = new ubis\model\entity\GestorTipoCasa();
        $oTiposCasaOpciones = $oTiposCasa->getListaTiposCasa();
        $oDesplegableTiposCasa = new Desplegable('tipo_casa', $oTiposCasaOpciones, $tipo_casa, true);

        $oDesplDelegaciones->setOpcion_sel($dl);
        $oDesplRegiones->setOpcion_sel($region);
        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'nombre_ubi' => $nombre_ubi,
            'plazas' => $plazas,
            'plazas_min' => $plazas_min,
            'num_sacd' => $num_sacd,
            'sv_chk' => $sv_chk,
            'sf_chk' => $sf_chk,
            'oDesplegableTiposCasa' => $oDesplegableTiposCasa,
            'oDesplDelegaciones' => $oDesplDelegaciones,
            'oDesplRegiones' => $oDesplRegiones,
        ];

        $oView->renderizar('cdc_form.phtml', $a_campos);
        break;
}