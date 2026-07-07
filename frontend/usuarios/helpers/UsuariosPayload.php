<?php

declare(strict_types=1);

namespace frontend\usuarios\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Desplegable;
use frontend\shared\web\DesplegableArray;
use src\configuracion\domain\value_objects\ConfigSnapshot;

final class UsuariosPayload
{
public static function postData(mixed $data): array
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

public static function signListaValores(array $valores): array
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
            $path = \frontend\shared\helpers\PayloadCoercion::string($spec['path'] ?? '');
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

public static function listaApplyNav(array $valores, string $id_sel, string $scroll_id): array
{
    if ($id_sel !== '') {
        $valores['select'] = $id_sel;
    }
    if ($scroll_id !== '') {
        $valores['scroll_id'] = $scroll_id;
    }

    return $valores;
}

public static function listaFromPayload(array $payload): array
{
    return [
        'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
        'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
        'valores' => self::signListaValores(ActividadesListaSupport::datos($payload['a_valores'] ?? [])),
    ];
}

public static function dataDesplFromPayload(array $raw): array
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
        $opcionSelOut = \frontend\shared\helpers\PayloadCoercion::string($opcionSel);
    }

    return [
        'tipo' => \frontend\shared\helpers\PayloadCoercion::string($raw['tipo'] ?? ''),
        'nom' => \frontend\shared\helpers\PayloadCoercion::string($raw['nom'] ?? ''),
        'aOpciones' => NotasFormSupport::desplegableOpciones($raw['aOpciones'] ?? []),
        'opcion_sel' => $opcionSelOut,
        'blanco' => self::desplegableBlanco($raw['blanco'] ?? ''),
        'accionConjunto' => \frontend\shared\helpers\PayloadCoercion::string($raw['accionConjunto'] ?? ''),
    ];
}

public static function desplegableBlanco(mixed $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value)) {
        return $value === 1 ? '1' : '';
    }

    return \frontend\shared\helpers\PayloadCoercion::string($value);
}

public static function formCamposFromPayload(array $payload): array
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
        $aDataDespl = self::dataDesplFromPayload($desplPayload);
    }

    return [
        'aOpcionesRoles' => NotasFormSupport::desplegableOpciones($payload['aOpcionesRoles'] ?? []),
        'id_role' => \frontend\shared\helpers\PayloadCoercion::string($payload['id_role'] ?? ''),
        'camposMas' => \frontend\shared\helpers\PayloadCoercion::string($payload['camposMas'] ?? ''),
        'ctx_guardar' => \frontend\shared\helpers\PayloadCoercion::string($payload['ctx_guardar'] ?? ''),
        'id_usuario' => NotasFormSupport::formScalar($payload['id_usuario'] ?? 0),
        'usuario' => \frontend\shared\helpers\PayloadCoercion::string($payload['usuario'] ?? ''),
        'quien' => \frontend\shared\helpers\PayloadCoercion::string($payload['quien'] ?? ''),
        'pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['pau'] ?? ''),
        'nom_usuario' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom_usuario'] ?? ''),
        'email' => \frontend\shared\helpers\PayloadCoercion::string($payload['email'] ?? ''),
        'chk_cambio_password' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_cambio_password'] ?? ''),
        'chk_has_2fa' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_has_2fa'] ?? ''),
        'obj' => \frontend\shared\helpers\PayloadCoercion::string($payload['obj'] ?? ''),
        'aDataDespl' => $aDataDespl,
    ];
}

public static function desplegableCasasFromData(?array $aDataDespl): Desplegable|DesplegableArray
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

public static function signedLink(mixed $spec): string
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

public static function roleFormFromPayload(array $payload): array
{
    $lista = self::listaFromPayload($payload);

    return [
        'role' => \frontend\shared\helpers\PayloadCoercion::string($payload['role'] ?? ''),
        'sf' => \frontend\shared\helpers\PayloadCoercion::string($payload['sf'] ?? ''),
        'chk_sf' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_sf'] ?? ''),
        'sv' => \frontend\shared\helpers\PayloadCoercion::string($payload['sv'] ?? ''),
        'chk_sv' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_sv'] ?? ''),
        'pau' => \frontend\shared\helpers\PayloadCoercion::string($payload['pau'] ?? ''),
        'dmz' => \frontend\shared\helpers\PayloadCoercion::string($payload['dmz'] ?? ''),
        'chk_dmz' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_dmz'] ?? ''),
        'permiso' => \frontend\shared\helpers\PayloadCoercion::string($payload['permiso'] ?? ''),
        'txt_sfsv' => \frontend\shared\helpers\PayloadCoercion::string($payload['txt_sfsv'] ?? ''),
        'aOpcionesPau' => NotasFormSupport::desplegableOpciones($payload['aOpcionesPau'] ?? []),
        'cabeceras' => $lista['cabeceras'],
        'botones' => $lista['botones'],
        'valores' => $lista['valores'],
    ];
}

public static function preferenciasFromPayload(array $payload): array
{
    $zona = $payload['zona_horaria'] ?? null;

    return [
        'layout' => \frontend\shared\helpers\PayloadCoercion::string($payload['layout'] ?? ''),
        'inicio' => \frontend\shared\helpers\PayloadCoercion::string($payload['inicio'] ?? ''),
        'oficina' => \frontend\shared\helpers\PayloadCoercion::string($payload['oficina'] ?? ''),
        'oficinas_posibles' => NotasFormSupport::desplegableOpciones($payload['oficinas_posibles'] ?? []),
        'estilo_azul_selected' => \frontend\shared\helpers\PayloadCoercion::string($payload['estilo_azul_selected'] ?? ''),
        'estilo_naranja_selected' => \frontend\shared\helpers\PayloadCoercion::string($payload['estilo_naranja_selected'] ?? ''),
        'estilo_verde_selected' => \frontend\shared\helpers\PayloadCoercion::string($payload['estilo_verde_selected'] ?? ''),
        'tipo_menu_h' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_menu_h'] ?? ''),
        'tipo_menu_v' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_menu_v'] ?? ''),
        'tipo_tabla_s' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_tabla_s'] ?? ''),
        'tipo_tabla_h' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_tabla_h'] ?? ''),
        'tipo_apellidos_ap_nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_apellidos_ap_nom'] ?? ''),
        'tipo_apellidos_nom_ap' => \frontend\shared\helpers\PayloadCoercion::string($payload['tipo_apellidos_nom_ap'] ?? ''),
        'idioma' => \frontend\shared\helpers\PayloadCoercion::string($payload['idioma'] ?? ''),
        'zona_horaria' => \frontend\shared\helpers\PayloadCoercion::string(empty($zona) ? 'UTC' : $zona),
    ];
}

public static function localesFromPayload(array $payload): array
{
    return NotasFormSupport::desplegableOpciones($payload['a_locales'] ?? []);
}

public static function zonaHorariaOpcionSel(string $zona_horaria): string
{
    $opciones = \DateTimeZone::listIdentifiers();
    $id = array_search($zona_horaria, $opciones, true);
    if ($id === false) {
        return '';
    }

    return \frontend\shared\helpers\PayloadCoercion::string($id);
}

public static function permMenuDlMapFromPayload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        $out[$key] = \frontend\shared\helpers\PayloadCoercion::int($value);
    }

    return $out;
}

public static function contactosFromPayload(mixed $raw): array
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
            'cargo' => \frontend\shared\helpers\PayloadCoercion::string($info['cargo'] ?? ''),
            'email' => \frontend\shared\helpers\PayloadCoercion::string($info['email'] ?? ''),
        ];
    }

    return $out;
}

public static function twoFaInfoFromPayload(array $payload): array
{
    return [
        'has_2fa' => !empty($payload['has_2fa']),
        'secret_2fa' => \frontend\shared\helpers\PayloadCoercion::string($payload['secret_2fa'] ?? ''),
    ];
}

public static function loginOkSessionFromResult(array $result): ?array
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
        'esquema' => \frontend\shared\helpers\PayloadCoercion::string($result['esquema'] ?? ''),
        'idioma' => \frontend\shared\helpers\PayloadCoercion::string($result['idioma'] ?? ''),
    ];
}

public static function cambiarIdioma(string $idioma = ''): void
{
    if ($idioma === '') {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        if (is_array($sessionAuth) && !empty($sessionAuth['idioma'])) {
            $idioma = \frontend\shared\helpers\PayloadCoercion::string($sessionAuth['idioma']);
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
                $idioma = \frontend\shared\helpers\PayloadCoercion::string($oConfig->getIdioma_default());
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

public static function recoveryRowFromFetch(mixed $row): ?array
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
}
