<?php

/**
 * Helpers compartidos del módulo frontend/usuarios.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Desplegable;
use frontend\shared\web\DesplegableArray;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * @return array<string, mixed>
 */
function usuarios_post_data(mixed $data): array
{
    if (!is_array($data)) {
        return [];
    }
    $out = [];
    foreach ($data as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

function usuarios_sel_first_item(mixed $a_sel): mixed
{
    if (!is_array($a_sel)) {
        return null;
    }
    foreach ($a_sel as $item) {
        return $item;
    }

    return null;
}

function usuarios_id_from_sel_item(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $parts = explode('#', $sel0, 2);
    $idRaw = $parts[0];

    return is_numeric($idRaw) ? (int) $idRaw : 0;
}

function usuarios_id_from_sel_second(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $parts = explode('#', $sel0, 2);
    if (!isset($parts[1])) {
        return 0;
    }

    return is_numeric($parts[1]) ? (int) $parts[1] : 0;
}

function usuarios_session_auth_int(string $key): int
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    if (!is_array($sessionAuth)) {
        return 0;
    }

    return tessera_imprimir_int($sessionAuth[$key] ?? 0);
}

function usuarios_session_auth_string(string $key, string $default = ''): string
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    if (!is_array($sessionAuth)) {
        return $default;
    }

    return tessera_imprimir_string($sessionAuth[$key] ?? $default);
}

function usuarios_request_string(string $key): string
{
    $merged = array_merge(usuarios_post_data($_GET), usuarios_post_data($_POST));

    return tessera_imprimir_string($merged[$key] ?? '');
}

/**
 * @param array<int|string, mixed> $valores
 * @return array<int|string, mixed>
 */
function usuarios_sign_lista_valores(array $valores): array
{
    $out = $valores;
    $baseUrl = AppUrlConfig::getPublicAppBaseUrl();
    foreach ($out as $idx => $fila) {
        if (!is_array($fila)) {
            continue;
        }
        $row = $fila;
        foreach ($row as $colKey => $cell) {
            if (!is_array($cell) || !isset($cell['link_spec'])) {
                continue;
            }
            $spec = $cell['link_spec'];
            if (!is_array($spec)) {
                continue;
            }
            $path = tessera_imprimir_string($spec['path'] ?? '');
            $queryRaw = $spec['query'] ?? null;
            $query = is_array($queryRaw) ? $queryRaw : [];
            if ($path === '') {
                continue;
            }
            $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);
            $cell['ira'] = HashFront::link($url);
            unset($cell['link_spec']);
            $row[$colKey] = $cell;
        }
        $out[$idx] = $row;
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $valores
 * @return array<int|string, mixed>
 */
function usuarios_lista_apply_nav(array $valores, string $id_sel, string $scroll_id): array
{
    if ($id_sel !== '') {
        $valores['select'] = $id_sel;
    }
    if ($scroll_id !== '') {
        $valores['scroll_id'] = $scroll_id;
    }

    return $valores;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 * }
 */
function usuarios_lista_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'valores' => usuarios_sign_lista_valores(actividades_lista_datos($payload['a_valores'] ?? [])),
    ];
}

/**
 * @param array<string, mixed> $raw
 * @return array{
 *     tipo: string,
 *     nom: string,
 *     aOpciones: array<int|string, string>,
 *     opcion_sel: string|list<string>,
 *     blanco: bool|string,
 *     accionConjunto: string,
 * }
 */
function usuarios_data_despl_from_payload(array $raw): array
{
    $opcionSel = $raw['opcion_sel'] ?? '';
    if (is_array($opcionSel)) {
        $selList = [];
        foreach ($opcionSel as $item) {
            if (is_string($item)) {
                $selList[] = $item;
            }
        }
        $opcionSelOut = $selList;
    } else {
        $opcionSelOut = tessera_imprimir_string($opcionSel);
    }

    return [
        'tipo' => tessera_imprimir_string($raw['tipo'] ?? ''),
        'nom' => tessera_imprimir_string($raw['nom'] ?? ''),
        'aOpciones' => notas_desplegable_opciones($raw['aOpciones'] ?? []),
        'opcion_sel' => $opcionSelOut,
        'blanco' => usuarios_desplegable_blanco($raw['blanco'] ?? ''),
        'accionConjunto' => tessera_imprimir_string($raw['accionConjunto'] ?? ''),
    ];
}

function usuarios_desplegable_blanco(mixed $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value)) {
        return $value === 1 ? '1' : '';
    }

    return tessera_imprimir_string($value);
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     aOpcionesRoles: array<int|string, string>,
 *     id_role: string,
 *     camposMas: string,
 *     ctx_guardar: string,
 *     id_usuario: int|string,
 *     usuario: string,
 *     quien: string,
 *     pau: string,
 *     nom_usuario: string,
 *     email: string,
 *     chk_cambio_password: string,
 *     chk_has_2fa: string,
 *     obj: string,
 *     aDataDespl: array{
 *         tipo: string,
 *         nom: string,
 *         aOpciones: array<int|string, string>,
 *         opcion_sel: string|list<string>,
 *         blanco: bool|string,
 *         accionConjunto: string,
 *     }|null,
 * }
 */
function usuarios_form_campos_from_payload(array $payload): array
{
    $desplRaw = $payload['aDataDespl'] ?? null;
    $aDataDespl = null;
    if (is_array($desplRaw)) {
        $desplPayload = [];
        foreach ($desplRaw as $k => $v) {
            if (is_string($k)) {
                $desplPayload[$k] = $v;
            }
        }
        $aDataDespl = usuarios_data_despl_from_payload($desplPayload);
    }

    return [
        'aOpcionesRoles' => notas_desplegable_opciones($payload['aOpcionesRoles'] ?? []),
        'id_role' => tessera_imprimir_string($payload['id_role'] ?? ''),
        'camposMas' => tessera_imprimir_string($payload['camposMas'] ?? ''),
        'ctx_guardar' => tessera_imprimir_string($payload['ctx_guardar'] ?? ''),
        'id_usuario' => notas_form_scalar($payload['id_usuario'] ?? 0),
        'usuario' => tessera_imprimir_string($payload['usuario'] ?? ''),
        'quien' => tessera_imprimir_string($payload['quien'] ?? ''),
        'pau' => tessera_imprimir_string($payload['pau'] ?? ''),
        'nom_usuario' => tessera_imprimir_string($payload['nom_usuario'] ?? ''),
        'email' => tessera_imprimir_string($payload['email'] ?? ''),
        'chk_cambio_password' => tessera_imprimir_string($payload['chk_cambio_password'] ?? ''),
        'chk_has_2fa' => tessera_imprimir_string($payload['chk_has_2fa'] ?? ''),
        'obj' => tessera_imprimir_string($payload['obj'] ?? ''),
        'aDataDespl' => $aDataDespl,
    ];
}

/**
 * @param array{
 *     tipo: string,
 *     nom: string,
 *     aOpciones: array<int|string, string>,
 *     opcion_sel: string|list<string>,
 *     blanco: bool|string,
 *     accionConjunto: string,
 * }|null $aDataDespl
 */
function usuarios_desplegable_casas_from_data(?array $aDataDespl): Desplegable|DesplegableArray
{
    if ($aDataDespl === null) {
        return new DesplegableArray();
    }
    if ($aDataDespl['tipo'] === 'simple') {
        $oDespl = new Desplegable();
        $oDespl->setNombre($aDataDespl['nom']);
        $oDespl->setOpciones($aDataDespl['aOpciones']);
        $oDespl->setOpcion_sel(is_string($aDataDespl['opcion_sel']) ? $aDataDespl['opcion_sel'] : '');
        $oDespl->setBlanco($aDataDespl['blanco']);

        return $oDespl;
    }
    $oDespl = new DesplegableArray();
    $oDespl->setAccionConjunto($aDataDespl['accionConjunto']);
    $oDespl->setNomConjunto($aDataDespl['nom']);
    $oDespl->setOpciones($aDataDespl['aOpciones']);
    if (is_array($aDataDespl['opcion_sel'])) {
        $oDespl->setSeleccionados($aDataDespl['opcion_sel']);
    } else {
        $oDespl->setSeleccionados($aDataDespl['opcion_sel']);
    }
    $oDespl->setBlanco($aDataDespl['blanco']);

    return $oDespl;
}

function usuarios_signed_link(mixed $spec): string
{
    if (!is_array($spec)) {
        return '';
    }
    $path = $spec['path'] ?? null;
    if (!is_string($path) || $path === '') {
        return '';
    }
    $parsed = ['path' => $path];
    $query = $spec['query'] ?? null;
    if (is_array($query)) {
        $q = [];
        foreach ($query as $k => $v) {
            $q[(string) $k] = $v;
        }
        if ($q !== []) {
            $parsed['query'] = $q;
        }
    }

    return HashFrontSignedLink::fromSpec($parsed);
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     role: string,
 *     sf: string,
 *     chk_sf: string,
 *     sv: string,
 *     chk_sv: string,
 *     pau: string,
 *     dmz: string,
 *     chk_dmz: string,
 *     permiso: string,
 *     txt_sfsv: string,
 *     aOpcionesPau: array<int|string, string>,
 *     cabeceras: list<array<string, mixed>|string>,
 *     botones: list<array<string, mixed>>,
 *     valores: array<int|string, mixed>,
 * }
 */
function usuarios_role_form_from_payload(array $payload): array
{
    $lista = usuarios_lista_from_payload($payload);

    return [
        'role' => tessera_imprimir_string($payload['role'] ?? ''),
        'sf' => tessera_imprimir_string($payload['sf'] ?? ''),
        'chk_sf' => tessera_imprimir_string($payload['chk_sf'] ?? ''),
        'sv' => tessera_imprimir_string($payload['sv'] ?? ''),
        'chk_sv' => tessera_imprimir_string($payload['chk_sv'] ?? ''),
        'pau' => tessera_imprimir_string($payload['pau'] ?? ''),
        'dmz' => tessera_imprimir_string($payload['dmz'] ?? ''),
        'chk_dmz' => tessera_imprimir_string($payload['chk_dmz'] ?? ''),
        'permiso' => tessera_imprimir_string($payload['permiso'] ?? ''),
        'txt_sfsv' => tessera_imprimir_string($payload['txt_sfsv'] ?? ''),
        'aOpcionesPau' => notas_desplegable_opciones($payload['aOpcionesPau'] ?? []),
        'cabeceras' => $lista['cabeceras'],
        'botones' => $lista['botones'],
        'valores' => $lista['valores'],
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     layout: string,
 *     inicio: string,
 *     oficina: string,
 *     oficinas_posibles: array<int|string, string>,
 *     estilo_azul_selected: string,
 *     estilo_naranja_selected: string,
 *     estilo_verde_selected: string,
 *     tipo_menu_h: string,
 *     tipo_menu_v: string,
 *     tipo_tabla_s: string,
 *     tipo_tabla_h: string,
 *     tipo_apellidos_ap_nom: string,
 *     tipo_apellidos_nom_ap: string,
 *     idioma: string,
 *     zona_horaria: string,
 * }
 */
function usuarios_preferencias_from_payload(array $payload): array
{
    $zona = $payload['zona_horaria'] ?? null;

    return [
        'layout' => tessera_imprimir_string($payload['layout'] ?? ''),
        'inicio' => tessera_imprimir_string($payload['inicio'] ?? ''),
        'oficina' => tessera_imprimir_string($payload['oficina'] ?? ''),
        'oficinas_posibles' => notas_desplegable_opciones($payload['oficinas_posibles'] ?? []),
        'estilo_azul_selected' => tessera_imprimir_string($payload['estilo_azul_selected'] ?? ''),
        'estilo_naranja_selected' => tessera_imprimir_string($payload['estilo_naranja_selected'] ?? ''),
        'estilo_verde_selected' => tessera_imprimir_string($payload['estilo_verde_selected'] ?? ''),
        'tipo_menu_h' => tessera_imprimir_string($payload['tipo_menu_h'] ?? ''),
        'tipo_menu_v' => tessera_imprimir_string($payload['tipo_menu_v'] ?? ''),
        'tipo_tabla_s' => tessera_imprimir_string($payload['tipo_tabla_s'] ?? ''),
        'tipo_tabla_h' => tessera_imprimir_string($payload['tipo_tabla_h'] ?? ''),
        'tipo_apellidos_ap_nom' => tessera_imprimir_string($payload['tipo_apellidos_ap_nom'] ?? ''),
        'tipo_apellidos_nom_ap' => tessera_imprimir_string($payload['tipo_apellidos_nom_ap'] ?? ''),
        'idioma' => tessera_imprimir_string($payload['idioma'] ?? ''),
        'zona_horaria' => tessera_imprimir_string(empty($zona) ? 'UTC' : $zona),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array<int|string, string>
 */
function usuarios_locales_from_payload(array $payload): array
{
    return notas_desplegable_opciones($payload['a_locales'] ?? []);
}

function usuarios_zona_horaria_opcion_sel(string $zona_horaria): string
{
    $opciones = DateTimeZone::listIdentifiers();
    $id = array_search($zona_horaria, $opciones, true);
    if ($id === false) {
        return '';
    }

    return tessera_imprimir_string($id);
}

/**
 * @return array<int|string, int>
 */
function usuarios_perm_menu_dl_map_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        $out[$key] = tessera_imprimir_int($value);
    }

    return $out;
}

/**
 * @return array<string, array{cargo: string, email: string}>
 */
function usuarios_contactos_from_payload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $nombre => $info) {
        if (!is_string($nombre)) {
            continue;
        }
        if (!is_array($info)) {
            continue;
        }
        $key = $nombre;
        $out[$key] = [
            'cargo' => tessera_imprimir_string($info['cargo'] ?? ''),
            'email' => tessera_imprimir_string($info['email'] ?? ''),
        ];
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{has_2fa: bool, secret_2fa: string}
 */
function usuarios_2fa_info_from_payload(array $payload): array
{
    return [
        'has_2fa' => !empty($payload['has_2fa']),
        'secret_2fa' => tessera_imprimir_string($payload['secret_2fa'] ?? ''),
    ];
}

/**
 * @return array{username: string, password: string, esquema: string, verification_code: string}
 */
function usuarios_login_input_from_post(): array
{
    $post = usuarios_post_data($_POST);

    return [
        'username' => tessera_imprimir_string($post['username'] ?? ''),
        'password' => tessera_imprimir_string($post['password'] ?? ''),
        'esquema' => tessera_imprimir_string($post['esquema'] ?? ''),
        'verification_code' => tessera_imprimir_string($post['verification_code'] ?? ''),
    ];
}

/**
 * @param array{
 *     ok: bool,
 *     error?: int,
 *     redirect_ayuda_2fa?: array{username: string, ubicacion: string, esquema: string},
 *     session_auth?: array<string, mixed>,
 *     session_config?: array<string, mixed>,
 *     esquema?: string,
 *     idioma?: string,
 *     sfsv?: int
 * } $result
 * @return array{session_auth: array<string, mixed>, session_config: array<string, mixed>, esquema: string, idioma: string}|null
 */
function usuarios_login_ok_session_from_result(array $result): ?array
{
    if (empty($result['ok'])) {
        return null;
    }
    $sessionAuth = $result['session_auth'] ?? null;
    $sessionConfig = $result['session_config'] ?? null;
    if (!is_array($sessionAuth) || !is_array($sessionConfig)) {
        return null;
    }

    return [
        'session_auth' => $sessionAuth,
        'session_config' => $sessionConfig,
        'esquema' => tessera_imprimir_string($result['esquema'] ?? ''),
        'idioma' => tessera_imprimir_string($result['idioma'] ?? ''),
    ];
}

function usuarios_cambiar_idioma(string $idioma = ''): void
{
    if ($idioma === '') {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        if (is_array($sessionAuth) && !empty($sessionAuth['idioma'])) {
            $idioma = tessera_imprimir_string($sessionAuth['idioma']);
        } elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && is_string($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $a_idiomas = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($a_idiomas as $a_idioma) {
                if ($idioma === '' && substr($a_idioma, 0, 2) === 'ca') {
                    $idioma = 'ca_ES.UTF-8';
                }
                if ($idioma === '' && substr($a_idioma, 0, 2) === 'es') {
                    $idioma = 'es_ES.UTF-8';
                }
                if ($idioma === '' && substr($a_idioma, 0, 2) === 'en') {
                    $idioma = 'en_US.UTF-8';
                }
                if ($idioma === '' && substr($a_idioma, 0, 2) === 'de') {
                    $idioma = 'de_DE.UTF-8';
                }
            }
        }
        if ($idioma === '') {
            $oConfig = $_SESSION['oConfig'] ?? null;
            if ($oConfig instanceof ConfigSnapshot) {
                $idioma = tessera_imprimir_string($oConfig->getIdioma_default());
            } else {
                $idioma = 'es_ES.UTF-8';
            }
        }
    }
    $domain = 'orbix';
    setlocale(LC_ALL, '');
    putenv("LC_ALL=''");
    putenv('LANGUAGE=');

    setlocale(LC_ALL, $idioma);
    putenv("LC_ALL={$idioma}");
    putenv("LANG={$idioma}");

    bindtextdomain($domain, OrbixRuntime::gettextLanguagesDir());
    textdomain($domain);
    bind_textdomain_codeset($domain, 'UTF-8');
}

/**
 * @return array{id_usuario: int, id_role: int}|null
 */
function usuarios_recovery_row_from_fetch(mixed $row): ?array
{
    if (!is_array($row)) {
        return null;
    }
    $idUsuario = $row['id_usuario'] ?? null;
    $idRole = $row['id_role'] ?? null;
    if (!is_numeric($idUsuario) || !is_numeric($idRole)) {
        return null;
    }

    return [
        'id_usuario' => (int) $idUsuario,
        'id_role' => (int) $idRole,
    ];
}

function usuarios_recovery_session_id_from_cookie(): ?string
{
    $cookie = $_COOKIE['PHPSESSID'] ?? null;
    if (!is_string($cookie) || $cookie === '') {
        return null;
    }

    return $cookie;
}
