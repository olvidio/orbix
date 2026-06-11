<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Ficha de atencion sacerdotal de un centro. Datos de negocio obtenidos del
 * backend a traves de {@see \src\encargossacd\application\CtrGetFichaData}
 * (`/src/encargossacd/ctr_get_ficha_data`). Aqui solo se arman los
 * `frontend\shared\web\Desplegable` / HTML de colaboradores y se pasan a la vista.
 */

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
$oPosicion = FrontBootstrap::boot();

$Qid_ubi = encargossacd_post_int('id_ubi');
$Qseleccion_sacd = encargossacd_post_int('seleccion_sacd');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_get_ficha_data', [
    'id_ubi' => $Qid_ubi,
    'seleccion_sacd' => $Qseleccion_sacd,
]);

$ficha = encargossacd_ctr_get_ficha_from_payload($data);
$mod = $ficha['mod'];
$tipo_centro = $ficha['tipo_centro'];
$num_enc = $ficha['num_enc'];
$chk_prelatura = $ficha['chk_prelatura'];
$chk_de_paso = $ficha['chk_de_paso'];
$chk_sssc = $ficha['chk_sssc'];
$aOpcionesSacd = $ficha['opciones_sacd'];
$aOpcionesSacdSssc = $ficha['opciones_sacd_sssc'];
$encargos = $ficha['encargos'];
$perm_des = $ficha['perm_des'];

$oDesplSacd = new Desplegable();
$oDesplSacd->setBlanco(true);
$oDesplSacd->setOpciones($aOpcionesSacd);

$sacd_num = [];
$cl_checked = [];
$mod_horario = [];
$a_id_enc = [];
$a_observ = [];
$a_desc_enc = [];
$dedic_m = [];
$dedic_t = [];
$dedic_v = [];
$dedic_sacd = [];
$dedic_ctr_m = [];
$dedic_ctr_t = [];
$dedic_ctr_v = [];
$otros_sacd = [];
$a_despl_titular = [];
$a_despl_suplente = [];
$a_Hash = [];

foreach ($encargos as $idx => $enc) {
    $e = $idx + 1;
    $id_enc_e = $enc['id_enc'];
    $mod_horario_e = $enc['mod_horario'];
    $sacd_num[$e] = $enc['sacd_num'];
    $cl_checked[$e] = $enc['cl_checked'];
    $mod_horario[$e] = $mod_horario_e;
    $a_id_enc[$e] = $id_enc_e;
    $a_observ[$e] = $enc['observ'];
    $a_desc_enc[$e] = $enc['desc_enc'];
    $dedic_m[$e] = $enc['dedic_m'];
    $dedic_t[$e] = $enc['dedic_t'];
    $dedic_v[$e] = $enc['dedic_v'];
    $dedic_sacd[$e] = $enc['dedic_sacd'];
    $dedic_ctr_m[$e] = $enc['dedic_ctr_m'];
    $dedic_ctr_t[$e] = $enc['dedic_ctr_t'];
    $dedic_ctr_v[$e] = $enc['dedic_ctr_v'];

    $oHash = new HashFront();
    $oHash->setArrayCamposHidden([
        'e' => $e,
        "mod_$e" => $mod,
        "id_enc_$e" => $id_enc_e,
        "id_ubi_$e" => $Qid_ubi,
        "tipo_centro_$e" => $tipo_centro,
        "mod_horario_$e" => $mod_horario_e,
    ]);
    $campos_form = $tipo_centro !== 'of'
        ? 'dedic_ctr_m!dedic_ctr_t!dedic_ctr_v!dedic_m!dedic_t!dedic_v!id_sacd_suplente!id_sacd_titular!observ'
        : 'dedic_ctr_m!dedic_ctr_t!dedic_ctr_v!dedic_m!dedic_t!dedic_v!observ';
    $oHash->setCamposForm($campos_form);
    $oHash->setcamposNo('id_sacd!sacd_num!cl!refresh');
    $a_Hash[$e] = $oHash;

    $oDesplTitular = new Desplegable();
    $oDesplTitular->setBlanco(true);
    $oDesplTitular->setOpciones($aOpcionesSacd);
    $oDesplTitular->setOpcion_sel(encargossacd_desplegable_opcion_sel($enc['actual_id_sacd_titular']));
    $a_despl_titular[$e] = $oDesplTitular;

    $oDesplSuplente = new Desplegable();
    $oDesplSuplente->setBlanco(true);
    $oDesplSuplente->setOpciones($aOpcionesSacd);
    $oDesplSuplente->setOpcion_sel(encargossacd_desplegable_opcion_sel($enc['actual_id_sacd_suplente']));
    $a_despl_suplente[$e] = $oDesplSuplente;

    $otros_sacd[$e] = encargossacd_construir_otros_sacd(
        $e,
        $mod_horario_e,
        $enc['colaboradores'],
        $dedic_m[$e],
        $dedic_t[$e],
        $dedic_v[$e],
        $dedic_sacd[$e],
        $aOpcionesSacd,
        $aOpcionesSacdSssc,
    );
}

if ($num_enc === 0) {
    $num_enc = count($encargos);
}

$url_ficha = 'frontend/encargossacd/controller/ctr_get_ficha.php';
$oHashFicha = new HashFront();
$oHashFicha->setUrl($url_ficha);
$oHashFicha->setCamposForm('id_ubi!seleccion_sacd');
$h_ficha = $oHashFicha->linkSinValParams();

$fase = 'AAA';

$a_campos = [
    'oPosicion' => $oPosicion,
    'num_enc' => $num_enc,
    'perm_des' => $perm_des,
    'a_Hash' => $a_Hash,
    'tipo_centro' => $tipo_centro,
    'fase' => $fase,
    'mod' => $mod,
    'sacd_num' => $sacd_num,
    'oDesplSacd' => $oDesplSacd,
    'a_despl_titular' => $a_despl_titular,
    'a_despl_suplente' => $a_despl_suplente,
    'cl_checked' => $cl_checked,
    'mod_horario' => $mod_horario,
    'a_id_enc' => $a_id_enc,
    'a_observ' => $a_observ,
    'a_desc_enc' => $a_desc_enc,
    'otros_sacd' => $otros_sacd,
    'dedic_m' => $dedic_m,
    'dedic_t' => $dedic_t,
    'dedic_v' => $dedic_v,
    'dedic_sacd' => $dedic_sacd,
    'dedic_ctr_m' => $dedic_ctr_m,
    'dedic_ctr_t' => $dedic_ctr_t,
    'dedic_ctr_v' => $dedic_ctr_v,
    'url_ficha' => $url_ficha,
    'id_ubi' => $Qid_ubi,
    'h_ficha' => $h_ficha,
    'chk_prelatura' => $chk_prelatura,
    'chk_de_paso' => $chk_de_paso,
    'chk_sssc' => $chk_sssc,
];

ajax_json_render_phtml('frontend\\encargossacd\\controller', 'ctr_get_ficha.phtml', $a_campos);
