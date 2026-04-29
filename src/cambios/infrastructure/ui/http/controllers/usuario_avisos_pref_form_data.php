<?php
/**
 * Endpoint JSON que devuelve la informacion necesaria para pintar el
 * formulario `usuario_avisos_pref` (edicion de un aviso de usuario/grupo).
 *
 * Sucesor del backend de `apps/cambios/controller/usuario_avisos_pref.php`.
 */

use src\cambios\application\UsuarioAvisosPrefFormData;
use frontend\shared\web\ContestarJson;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use src\actividades\application\ActividadTipo;
use src\actividades\domain\entity\TiposActividades;

require_once 'frontend/shared/global_header_front.inc';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_usuario = (int)strtok((string)$a_sel[0], '#');
    $id_item_usuario_objeto = (int)strtok('#');
} else {
    $id_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $id_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$input = [
    'id_usuario' => $id_usuario,
    'id_item_usuario_objeto' => $id_item_usuario_objeto,
    'salida' => (string)filter_input(INPUT_POST, 'salida'),
    'quien' => (string)filter_input(INPUT_POST, 'quien'),
];

$result = UsuarioAvisosPrefFormData::execute($input);
$error = (string)$result['error'];
unset($result['error']);

if ($error === '') {
    $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
    $paths = isset($result['paths']) && is_array($result['paths']) ? $result['paths'] : [];
    $abs = static function (string $rel) use ($base): string {
        return $rel !== '' ? $base . '/' . ltrim($rel, '/') : '';
    };

    $result['url_guardar_objeto'] = $abs((string)($paths['cambio_usuario_objeto_pref_guardar'] ?? ''));
    $result['url_guardar_propiedades'] = $abs((string)($paths['cambio_usuario_propiedad_pref_guardar_todas'] ?? ''));
    $result['url_preview_cond'] = $abs((string)($paths['cambio_usuario_propiedad_pref_preview'] ?? ''));
    $result['url_get_propiedades'] = $abs((string)($paths['usuario_avisos_pref_propiedades'] ?? ''));
    $result['url_get_condicion'] = $abs((string)($paths['usuario_avisos_pref_condicion'] ?? ''));
    $result['url_get_fases'] = $abs((string)($paths['usuario_avisos_pref_fases'] ?? ''));

    $hm = isset($result['hash_main']) && is_array($result['hash_main']) ? $result['hash_main'] : [];
    $oHash = new HashFront();
    $oHash->setCamposForm((string)($hm['campos_form'] ?? ''));
    $oHash->setCamposNo((string)($hm['campos_no'] ?? ''));
    $chk = (string)($hm['campos_chk'] ?? '');
    if ($chk !== '') {
        $oHash->setCamposChk($chk);
    }
    $hidden = $hm['campos_hidden'] ?? [];
    $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);
    $result['hash_form_html'] = $oHash->getCamposHtml();

    $hf = isset($result['hash_ajax_fases']) && is_array($result['hash_ajax_fases']) ? $result['hash_ajax_fases'] : [];
    $oHashFases = new HashFront();
    $oHashFases->setUrl($abs((string)($hf['path'] ?? '')));
    $oHashFases->setCamposForm((string)($hf['campos_form'] ?? ''));
    $result['h_actualizar'] = $oHashFases->linkSinValParams();

    $hp = isset($result['hash_ajax_propiedades']) && is_array($result['hash_ajax_propiedades']) ? $result['hash_ajax_propiedades'] : [];
    $oHashProp = new HashFront();
    $oHashProp->setUrl($abs((string)($hp['path'] ?? '')));
    $oHashProp->setCamposForm((string)($hp['campos_form'] ?? ''));
    $result['h_propiedades'] = $oHashProp->linkSinValParams();

    $hm2 = isset($result['hash_ajax_mod']) && is_array($result['hash_ajax_mod']) ? $result['hash_ajax_mod'] : [];
    $oHashMod = new HashFront();
    $oHashMod->setUrl($abs((string)($hm2['path'] ?? '')));
    $oHashMod->setCamposForm((string)($hm2['campos_form'] ?? ''));
    $result['h_mod'] = $oHashMod->linkSinValParams();

    $perm_jefe = (bool)($result['perm_jefe'] ?? false);
    $id_tipo_res = (string)($result['id_tipo_activ'] ?? '');
    $oActividadTipo = new ActividadTipo();
    $oActividadTipo->setSfsvAll(false);
    if ($id_tipo_res !== '') {
        $oActividadTipo->setId_tipo_activ($id_tipo_res);
    } else {
        $oTipoActiv = new TiposActividades();
        $oTipoActiv->setSfsvText((string)($result['sfsv_text'] ?? ''));
        $oActividadTipo->setSfsv($oTipoActiv->getSfsvText());
        $oActividadTipo->setAsistentes($oTipoActiv->getAsistentesText());
        $oActividadTipo->setActividad($oTipoActiv->getActividadText());
        $oActividadTipo->setNom_tipo($oTipoActiv->getNom_tipoText());
    }
    $oActividadTipo->setPara('cambios');
    $oActividadTipo->setQue('buscar');
    $oActividadTipo->setPerm_jefe($perm_jefe);
    $result['actividad_tipo_html'] = $oActividadTipo->getHtml();

    unset(
        $result['hash_main'],
        $result['paths'],
        $result['hash_ajax_fases'],
        $result['hash_ajax_propiedades'],
        $result['hash_ajax_mod'],
    );
}

ContestarJson::enviar($error, $result);
