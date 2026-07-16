<?php

declare(strict_types=1);

/**
 * Diagnóstico hash formulario calendario (planning) → actividad_mutacion_ajax.
 * Uso: php tools/fix/debug_calendario_hash.php
 */

use frontend\actividades\helpers\ActividadesMutacionSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/../libs/vendor/autoload.php';
require_once __DIR__ . '/../src/shared/load_env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SERVER['REQUEST_URI'] = AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/actividades/controller/actividad_mutacion_ajax.php';
$_SERVER['PHP_SELF'] = '/frontend/actividades/controller/actividad_mutacion_ajax.php';
$_SERVER['REQUEST_SCHEME'] = 'https';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '443';

$urlMutacionAjax = AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/actividades/controller/actividad_mutacion_ajax.php';

$oHash = new HashFront();
$oHash->setUrl($urlMutacionAjax);
$oHash->setArraycamposHidden([
    'id_tipo_activ' => '123456',
    'id_activ' => '99',
    'ssfsv' => '1',
]);
$oHash->setCamposForm(ActividadesMutacionSupport::calendarioFormHashCamposForm());
$oHash->setCamposNo('id_tipo_activ!mod');
$html = $oHash->getCamposHtml();

/** Campos que el hash `h` espera (nombres, sin valores). */
$expected = calendario_hash_expected_field_names($oHash);

$post = calendario_hash_sample_post($html);
$post['id_tipo_activ'] = '1123456';

$report = calendario_hash_validate_report($post);

echo "Expected h fields (" . count($expected) . "):\n";
echo implode(', ', $expected) . "\n\n";

echo "POST fields after hno unset (" . count($report['post_fields']) . "):\n";
echo implode(', ', $report['post_fields']) . "\n\n";

echo "Extra in POST (break hash): " . implode(', ', $report['extra']) . "\n";
echo "Missing in POST: " . implode(', ', $report['missing']) . "\n";
echo "hh match: " . ($report['hh_ok'] ? 'yes' : 'no — ' . $report['hh_detail']) . "\n";
echo "h match: " . ($report['h_ok'] ? 'yes' : 'no') . "\n";
if (!$report['h_ok']) {
    echo "h expected orig: " . $report['h_orig_expected'] . "\n";
    echo "h actual orig:   " . $report['h_orig_actual'] . "\n";
}

/**
 * @return list<string>
 */
function calendario_hash_expected_field_names(HashFront $oHash): array
{
    $ref = new ReflectionClass($oHash);
    $addHidden = $ref->getMethod('addHiddenToForm');
    $addHidden->setAccessible(true);
    $addHidden->invoke($oHash);

    $getCamposForm = $ref->getMethod('getCamposForm');
    $getCamposForm->setAccessible(true);
    $sCamposForm = (string) $getCamposForm->invoke($oHash);

    $getCamposNo = $ref->getMethod('getCamposNo');
    $getCamposNo->setAccessible(true);
    $camposNo = explode('!', (string) $getCamposNo->invoke($oHash));

    $fields = array_filter(explode('!', $sCamposForm));
    sort($fields);
    $out = [];
    foreach ($fields as $campo) {
        if ($campo !== '' && !in_array($campo, $camposNo, true)) {
            $out[] = $campo;
        }
    }

    return $out;
}

/**
 * @return array<string, string>
 */
function calendario_hash_sample_post(string $html): array
{
    $post = [
        'mod' => 'editar',
        'dl_org' => 'dlbf',
        'f_fin' => '01/01/2026',
        'f_ini' => '01/01/2026',
        'h_fin' => '10:00',
        'h_ini' => '09:00',
        'extendida' => '',
        'iactividad_val' => '12',
        'iasistentes_val' => '34',
        'id_repeticion' => '0',
        'id_ubi' => '100',
        'inom_tipo_val' => '56',
        'isfsv_val' => '1',
        'lugar_esp' => '',
        'nivel_stgr' => '1',
        'nom_activ' => 'test',
        'nombre_ubi' => 'ubi',
        'observ' => '',
        'plazas' => '10',
        'precio' => '0',
        'publicado' => 'true',
        'status' => '2',
        'id_tarifa' => '1',
        'idioma' => 'es',
        'id_activ' => '99',
        'ssfsv' => '1',
        'id_tipo_activ' => '123456',
    ];

    foreach (['h', 'hh', 'hhc', 'hno'] as $meta) {
        if (preg_match('/name="' . $meta . '" value="([^"]*)"/', $html, $m)) {
            $post[$meta] = $m[1];
        }
    }

    return $post;
}

/**
 * @param array<string, string> $aPOST
 * @return array{
 *   post_fields: list<string>,
 *   extra: list<string>,
 *   missing: list<string>,
 *   hh_ok: bool,
 *   hh_detail: string,
 *   h_ok: bool,
 *   h_orig_expected: string,
 *   h_orig_actual: string,
 * }
 */
function calendario_hash_validate_report(array $aPOST): array
{
    $h1 = $aPOST['h'] ?? '';
    $hh = $aPOST['hh'] ?? '';
    $hhc = $aPOST['hhc'] ?? '';
    $hno = $aPOST['hno'] ?? '';

    $post = $aPOST;
    if ($hno !== '') {
        foreach (explode('!', $hno) as $campo) {
            unset($post[$campo]);
        }
    }

    $hhOk = true;
    $hhDetail = '';
    $aCamposHh = [];
    foreach (explode('!', $hhc) as $campo) {
        if ($campo === '') {
            continue;
        }
        $aCamposHh[$campo] = $post[$campo] ?? '';
    }
    $ref = new ReflectionClass(HashFront::class);
    $getHashArray = $ref->getMethod('getHashArray');
    $getHashArray->setAccessible(true);
    $h2hh = $getHashArray->invoke(null, $aCamposHh);
    if ($hh !== $h2hh['hash']) {
        $hhOk = false;
        $hhDetail = 'expected hh from ' . json_encode($aCamposHh) . ' got ' . $h2hh['hash'];
    }

    unset($post['PHPSESSID'], $post['atras'], $post['h'], $post['horig'], $post['hh'], $post['hhc'], $post['hhorig'], $post['hno'], $post['hchk'], $post['hnov']);
    $post = HashFront::stripPostCamposUiDinamicos($post);
    ksort($post);

    $oHash = new HashFront();
    $oHash->setCamposForm(ActividadesMutacionSupport::calendarioFormHashCamposForm());
    $oHash->setCamposNo('id_tipo_activ!mod');
    $expected = calendario_hash_expected_field_names($oHash);

    $postFields = array_keys($post);
    sort($postFields);

    $extra = array_values(array_diff($postFields, $expected));
    $missing = array_values(array_diff($expected, $postFields));

    $h2 = $getHashArray->invoke(null, $post, 1);

    return [
        'post_fields' => $postFields,
        'extra' => $extra,
        'missing' => $missing,
        'hh_ok' => $hhOk,
        'hh_detail' => $hhDetail,
        'h_ok' => $h1 === $h2['hash'],
        'h_orig_expected' => calendario_hash_h_orig_from_fields($expected),
        'h_orig_actual' => $h2['orig'],
    ];
}

/**
 * @param list<string> $fields
 */
function calendario_hash_h_orig_from_fields(array $fields): string
{
    $aCampos = [];
    foreach ($fields as $f) {
        $aCampos[$f] = '';
    }
    $aCampos['hnov'] = 1;
    ksort($aCampos);
    $s = http_build_query($aCampos, '', '&');

    return str_replace('%21', '!', $s);
}
