<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\CuadrosLabor;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
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
require_once("frontend/shared/global_header_front.inc");

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');


// Clase auxiliar para usar el trait en contexto procedural
$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object
    {
        return $this->getRepository($entityType);
    }
};

function getRepository(string $obj_pau): object
{
    global $repositoryProvider;
    return $repositoryProvider->get($obj_pau);
}


$es_de_dl = FALSE;
if (!empty($Qnuevo)) {
    $tipo_ubi = (string)filter_input(INPUT_POST, 'tipo_ubi');
    if (empty($Qobj_pau)) {
        switch ($tipo_ubi) {
            case 'ctrdl':
            case 'ctrsf':
                $Qobj_pau = 'CentroDl';
                break;
            case 'ctrex':
                $Qobj_pau = 'CentroEx';
                break;
            case 'cdcdl':
                $Qobj_pau = 'CasaDl';
                break;
            case 'cdcex':
                $Qobj_pau = 'CasaEx';
                break;
        }
    }
    if (empty($Qobj_pau)) {
        exit(_("falta definir obj_pau"));
    }
    $UbiRepository = getRepository($Qobj_pau);

    $dl = (string)filter_input(INPUT_POST, 'dl');
    $region = (string)filter_input(INPUT_POST, 'region');
    $nombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');
    $nombre_ubi = urldecode($nombre_ubi);

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

    $newIdAuto = $UbiRepository->getNewId();
    $Qid_ubi = $UbiRepository->getNewIdUbi($newIdAuto);

    // para evitar poner el use y que el ide no detecte que se usa:
    $obj_pau_full = 'src\ubis\domain\entity\\' . $Qobj_pau;
    $oUbi = new $obj_pau_full();
    $oUbi->setNombre_ubi($nombre_ubi);
    $oUbi->setDl($dl);
    $oUbi->setRegion($region);
    $oUbi->setTipo_ubi($tipo_ubi);
    $oUbi->setActive(true);

    if (strstr($tipo_ubi, 'cdc')) {
        if (ConfigGlobal::mi_sfsv() === 1) {
            $oUbi->setSv(TRUE);
        }
        if (ConfigGlobal::mi_sfsv() === 2) {
            $oUbi->setSf(TRUE);
        }
    }

    $Qid_ubi = '';
    $id_direccion = '';
} else {
    if (empty($Qobj_pau)) {
        exit(_("falta definir obj_pau"));
    }
    $UbiRepository = getRepository($Qobj_pau);
    $oUbi = $UbiRepository->findById($Qid_ubi);

    $tipo_ubi = $oUbi->getTipo_ubi();
    $dl = $oUbi->getDl();
    $id_ubi = $oUbi->getId_ubi();
    $region = $oUbi->getRegion();
    $nombre_ubi = $oUbi->getNombre_ubi();
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
        if ($tipo_ubi === 'ctrdl') {
            $oUbi_new = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->findById($id_ubi);
            // comprobar que realmente es el mismo:
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                $Qobj_pau = 'CentroDl';
            }
        }
        if ($tipo_ubi === 'cdcdl') {
            $oUbi_new = $GLOBALS['container']->get(CasaDlRepositoryInterface::class)->findById($id_ubi);
            // comprobar que realmente es el mismo:
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                $Qobj_pau = 'CasaDl';
            }
        }
    }
}

//----------------------------------Permisos según el usuario
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

$oPermActiv = new CuadrosLabor();

$chk = $oUbi->isActive() ? 'checked' : '';
$campos_chk = 'active!sv!sf';

$camposForm = 'que!dl!tipo_ubi!active!region!nombre_ubi';
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

$dlOpc = $dl;
$regionOpc = $region;
if ($tipo_ubi === 'ctrdl' || $tipo_ubi === 'ctrsf') {
    $dlOpc = empty($dl) ? ConfigGlobal::mi_delef() : $dl;
    $regionOpc = empty($region) ? ConfigGlobal::mi_region() : $region;
} elseif ($tipo_ubi === 'cdcdl') {
    $dlOpc = empty($dl) ? ConfigGlobal::mi_dele() : $dl;
    $regionOpc = empty($region) ? ConfigGlobal::mi_region() : $region;
}

$dataOpciones = PostRequest::getDataFromUrl('/src/ubis/ubis_editar_data', [
    'obj_pau' => $Qobj_pau,
    'tipo_ubi' => $tipo_ubi,
    'dl' => $dlOpc,
    'region' => $regionOpc,
]);
if (!empty($dataOpciones['error'])) {
    exit((string)$dataOpciones['error']);
}

$oView = new ViewNewPhtml('frontend\ubis\controller');

switch ($tipo_ubi) {
    case "ctrdl":
    case "ctrsf":
        $cdc = $oUbi->isCdc();
        $chk_cdc = is_true($cdc) ? 'checked' : '';
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

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            //'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $tipo_ctr,
            'id_ctr_padre' => $id_ctr_padre,
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
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'] ?? [],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'] ?? [],
        ];

        $oView->renderizar('ctrdl_form.phtml', $a_campos);
        break;
    case "ctrex":
        $cdc = $oUbi->isCdc();
        $chk_cdc = is_true($cdc) ? 'checked' : '';
        $tipo_labor = $oUbi->getTipo_labor();
        $id_ctr_padre = $oUbi->getId_ctr_padre();
        $tipo_ctr = $oUbi->getTipo_ctr();

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            //'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_ctr' => $tipo_ctr,
            'id_ctr_padre' => $id_ctr_padre,
            'chk_cdc' => $chk_cdc,
            'tipo_labor' => $tipo_labor,
            'oPermActiv' => $oPermActiv,
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_ctr' => $dataOpciones['opciones_tipo_ctr'] ?? [],
            'opciones_id_ctr_padre' => $dataOpciones['opciones_id_ctr_padre'] ?? [],
        ];

        $oView->renderizar('ctrex_form.phtml', $a_campos);
        break;
    case "cdcdl":
    case "cdcex":
        // OJO LAS CASAS pueden ser comunes. la dl es sin 'f'.
        if ($tipo_ubi === "cdcdl") {
            $dl = empty($dl) ? ConfigGlobal::mi_dele() : $dl;
            $region = empty($region) ? ConfigGlobal::mi_region() : $region;
        }

        $tipo_casa = $oUbi->getTipo_casa();
        $plazas = $oUbi->getPlazas();
        $plazas_min = $oUbi->getPlazas_min();
        $num_sacd = $oUbi->getNum_sacd();
        $sv = $oUbi->isSv();
        $sf = $oUbi->isSf();

        $sv_chk = is_true($sv) ? 'checked' : '';
        $sf_chk = is_true($sf) ? 'checked' : '';

        $a_campos = ['botones' => $botones,
            'oPosicion' => $oPosicion,
            //'obj' => $obj,
            'oHash' => $oHash,
            'tipo_ubi' => $tipo_ubi,
            'chk' => $chk,
            'dl' => $dl,
            'region' => $region,
            'nombre_ubi' => $nombre_ubi,
            'tipo_casa' => $tipo_casa,
            'plazas' => $plazas,
            'plazas_min' => $plazas_min,
            'num_sacd' => $num_sacd,
            'sv_chk' => $sv_chk,
            'sf_chk' => $sf_chk,
            'opciones_dl' => $dataOpciones['opciones_dl'] ?? [],
            'opciones_region' => $dataOpciones['opciones_region'] ?? [],
            'opciones_tipo_casa' => $dataOpciones['opciones_tipo_casa'] ?? [],
        ];

        $oView->renderizar('cdc_form.phtml', $a_campos);
        break;
}
