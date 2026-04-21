<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use web\Desplegable;
use web\Hash;

/**
 * Ficha de atencion sacerdotal de un centro. Datos de negocio obtenidos del
 * backend a traves de {@see \src\encargossacd\application\CtrGetFichaData}
 * (`/src/encargossacd/ctr_get_ficha_data`). Aqui solo se arman los
 * `web\Desplegable` / HTML de colaboradores y se pasan a la vista.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qseleccion_sacd = (int)filter_input(INPUT_POST, 'seleccion_sacd');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_get_ficha_data', [
    'id_ubi' => $Qid_ubi,
    'seleccion_sacd' => $Qseleccion_sacd,
]);

$mod = (string)($data['mod'] ?? 'nuevo');
$tipo_centro = (string)($data['tipo_centro'] ?? '');
$num_enc = (int)($data['num_enc'] ?? 0);
$chk_prelatura = (string)($data['chk_prelatura'] ?? '');
$chk_de_paso = (string)($data['chk_de_paso'] ?? '');
$chk_sssc = (string)($data['chk_sssc'] ?? '');
$aOpcionesSacd = is_array($data['opciones_sacd'] ?? null) ? $data['opciones_sacd'] : [];
$aOpcionesSacdSssc = is_array($data['opciones_sacd_sssc'] ?? null) ? $data['opciones_sacd_sssc'] : null;
$encargos = is_array($data['encargos'] ?? null) ? $data['encargos'] : [];
$perm_des = (bool)($data['perm_des'] ?? false);

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
    $e = (int)$idx + 1;
    $id_enc_e = (int)($enc['id_enc'] ?? 0);
    $mod_horario_e = (int)($enc['mod_horario'] ?? 0);
    $sacd_num[$e] = (int)($enc['sacd_num'] ?? 1);
    $cl_checked[$e] = (string)($enc['cl_checked'] ?? '');
    $mod_horario[$e] = $mod_horario_e;
    $a_id_enc[$e] = $id_enc_e;
    $a_observ[$e] = (string)($enc['observ'] ?? '');
    $a_desc_enc[$e] = (string)($enc['desc_enc'] ?? '');
    $dedic_m[$e] = is_array($enc['dedic_m'] ?? null) ? $enc['dedic_m'] : [''];
    $dedic_t[$e] = is_array($enc['dedic_t'] ?? null) ? $enc['dedic_t'] : [''];
    $dedic_v[$e] = is_array($enc['dedic_v'] ?? null) ? $enc['dedic_v'] : [''];
    $dedic_sacd[$e] = is_array($enc['dedic_sacd'] ?? null) ? $enc['dedic_sacd'] : [''];
    $dedic_ctr_m[$e] = (string)($enc['dedic_ctr_m'] ?? '');
    $dedic_ctr_t[$e] = (string)($enc['dedic_ctr_t'] ?? '');
    $dedic_ctr_v[$e] = (string)($enc['dedic_ctr_v'] ?? '');

    $oHash = new Hash();
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
    $oDesplTitular->setOpcion_sel((int)($enc['actual_id_sacd_titular'] ?? 0));
    $a_despl_titular[$e] = $oDesplTitular;

    $oDesplSuplente = new Desplegable();
    $oDesplSuplente->setBlanco(true);
    $oDesplSuplente->setOpciones($aOpcionesSacd);
    $oDesplSuplente->setOpcion_sel((int)($enc['actual_id_sacd_suplente'] ?? 0));
    $a_despl_suplente[$e] = $oDesplSuplente;

    $otros_sacd[$e] = construir_otros_sacd(
        $e,
        $mod_horario_e,
        is_array($enc['colaboradores'] ?? null) ? $enc['colaboradores'] : [],
        $dedic_m[$e],
        $dedic_t[$e],
        $dedic_v[$e],
        $dedic_sacd[$e],
        $aOpcionesSacd,
        $aOpcionesSacdSssc,
    );
}

if ($num_enc === 0) {
    // La vista iteraria hasta num_enc=0 y no pintaria nada: forzamos al menos
    // una entrada vacia equivalente al "mod = nuevo" del backend (ya viene en
    // el payload, pero `num_enc` puede ser 0 si el endpoint no devuelve encargos
    // y no aplica el caso "nuevo").
    $num_enc = count($encargos);
}

$url_ficha = 'frontend/encargossacd/controller/ctr_get_ficha.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ficha);
$oHashFicha->setCamposForm('id_ubi!seleccion_sacd');
$h_ficha = $oHashFicha->linkSinVal();

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

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('ctr_get_ficha.phtml', $a_campos);

/**
 * Construye el bloque HTML de colaboradores (sacd adicionales) de un encargo.
 * El desplegable de cada colaborador se construye en frontend (patron del
 * refactor: backend solo devuelve `opciones` value=>label).
 *
 * @param array<int, array<string, mixed>> $colaboradores
 * @param array<int, string>               $dedicM
 * @param array<int, string>               $dedicT
 * @param array<int, string>               $dedicV
 * @param array<int, string>               $dedicSacd
 * @param array<string, string>            $opcionesBase
 * @param array<string, string>|null       $opcionesConSssc
 */
function construir_otros_sacd(
    int $e,
    int $mod_horario_e,
    array $colaboradores,
    array $dedicM,
    array $dedicT,
    array $dedicV,
    array $dedicSacd,
    array $opcionesBase,
    ?array $opcionesConSssc,
): string {
    if (count($colaboradores) === 0) {
        return '';
    }

    $html = '';
    foreach ($colaboradores as $colab) {
        $s = (int)($colab['s'] ?? 0);
        $id_nom = (int)($colab['id_nom'] ?? 0);
        $necesitaSssc = !empty($colab['necesita_sssc']);

        $opciones = $necesitaSssc && $opcionesConSssc !== null ? $opcionesConSssc : $opcionesBase;
        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones($opciones);
        $oDespl->setOpcion_sel($id_nom);

        $html .= "<tr><td>sacd $s:</td><td colspan=3 class=contenido><select name=id_sacd[$s]>";
        $html .= $oDespl->options();
        $html .= '</td></tr><tr><td class=etiqueta >' . ucfirst(_('dedicación')) . '</td>';

        if ($mod_horario_e === 3) {
            $txtHorario = (string)($dedicSacd[$s] ?? '');
            $html .= '<td>' . $txtHorario . '</td></tr><tr>';
        } else {
            $m = (string)($dedicM[$s] ?? '');
            $t = (string)($dedicT[$s] ?? '');
            $v = (string)($dedicV[$s] ?? '');
            $html .= "<td><input type=text size=1 name=dedic_m[$s] value=$m>" . _('mañanas');
            $html .= "</td><td><input type=text size=1 name=dedic_t[$s] value=$t>" . _('tarde 1ª hora');
            $html .= "</td><td><input type=text size=1 name=dedic_v[$s] value=$v>" . _('tarde 2ª hora');
            $html .= '</td></tr><tr>';
        }
    }

    return $html;
}
