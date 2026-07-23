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
use frontend\shared\session\SessionConfig;

final class UsuariosPayload
{
    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param array<int|string, mixed> $valores
     * @return array<int|string, mixed>
     */
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
                $path = PayloadCoercion::string($spec['path'] ?? '');
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

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     cabeceras: list<array<string, mixed>|string>,
     *     botones: list<array<string, mixed>>,
     *     valores: array<int|string, mixed>,
     * }
     */
    public static function listaFromPayload(array $payload): array
    {
        return [
            'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
            'valores' => self::signListaValores(ActividadesListaSupport::datos($payload['a_valores'] ?? [])),
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
            $opcionSelOut = PayloadCoercion::string($opcionSel);
        }

        return [
            'tipo' => PayloadCoercion::string($raw['tipo'] ?? ''),
            'nom' => PayloadCoercion::string($raw['nom'] ?? ''),
            'aOpciones' => NotasFormSupport::desplegableOpciones($raw['aOpciones'] ?? []),
            'opcion_sel' => $opcionSelOut,
            'blanco' => self::desplegableBlanco($raw['blanco'] ?? ''),
            'accionConjunto' => PayloadCoercion::string($raw['accionConjunto'] ?? ''),
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

        return PayloadCoercion::string($value);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     aOpcionesRoles: array<int|string, string>,
     *     id_role: string,
     *     camposMas: string,
     *     ctx_guardar: string,
     *     id_usuario: int|float|string,
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
            'id_role' => PayloadCoercion::string($payload['id_role'] ?? ''),
            'camposMas' => PayloadCoercion::string($payload['camposMas'] ?? ''),
            'ctx_guardar' => PayloadCoercion::string($payload['ctx_guardar'] ?? ''),
            'id_usuario' => NotasFormSupport::formScalar($payload['id_usuario'] ?? 0),
            'usuario' => PayloadCoercion::string($payload['usuario'] ?? ''),
            'quien' => PayloadCoercion::string($payload['quien'] ?? ''),
            'pau' => PayloadCoercion::string($payload['pau'] ?? ''),
            'nom_usuario' => PayloadCoercion::string($payload['nom_usuario'] ?? ''),
            'email' => PayloadCoercion::string($payload['email'] ?? ''),
            'chk_cambio_password' => PayloadCoercion::string($payload['chk_cambio_password'] ?? ''),
            'chk_has_2fa' => PayloadCoercion::string($payload['chk_has_2fa'] ?? ''),
            'obj' => PayloadCoercion::string($payload['obj'] ?? ''),
            'aDataDespl' => $aDataDespl,
        ];
    }

    /**
     * @param array<string, mixed>|null $aDataDespl
     */
    public static function desplegableCasasFromData(?array $aDataDespl): Desplegable|DesplegableArray
    {
        if ($aDataDespl === null) {
            return new DesplegableArray();
        }
        if ($aDataDespl['tipo'] === 'simple') {
            $oDespl = new Desplegable();
            $oDespl->setNombre(PayloadCoercion::string($aDataDespl['nom'] ?? ''));
            $oDespl->setOpciones(NotasFormSupport::desplegableOpciones($aDataDespl['aOpciones'] ?? []));
            $oDespl->setOpcion_sel(is_string($aDataDespl['opcion_sel']) ? $aDataDespl['opcion_sel'] : '');
            $oDespl->setBlanco(self::desplegableBlanco($aDataDespl['blanco'] ?? ''));

            return $oDespl;
        }
        $oDespl = new DesplegableArray();
        $oDespl->setAccionConjunto(PayloadCoercion::string($aDataDespl['accionConjunto'] ?? ''));
        $oDespl->setNomConjunto(PayloadCoercion::string($aDataDespl['nom'] ?? ''));
        $oDespl->setOpciones(NotasFormSupport::desplegableOpciones($aDataDespl['aOpciones'] ?? []));
        $opcionSel = $aDataDespl['opcion_sel'];
        if (is_array($opcionSel)) {
            $selList = [];
            foreach ($opcionSel as $item) {
                if (is_string($item)) {
                    $selList[] = $item;
                }
            }
            $oDespl->setSeleccionados($selList);
        } else {
            $oDespl->setSeleccionados(PayloadCoercion::string($opcionSel));
        }
        $oDespl->setBlanco(self::desplegableBlanco($aDataDespl['blanco'] ?? ''));

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
                $q[PayloadCoercion::string($k)] = $v;
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
    public static function roleFormFromPayload(array $payload): array
    {
        $lista = self::listaFromPayload($payload);

        return [
            'role' => PayloadCoercion::string($payload['role'] ?? ''),
            'sf' => PayloadCoercion::string($payload['sf'] ?? ''),
            'chk_sf' => PayloadCoercion::string($payload['chk_sf'] ?? ''),
            'sv' => PayloadCoercion::string($payload['sv'] ?? ''),
            'chk_sv' => PayloadCoercion::string($payload['chk_sv'] ?? ''),
            'pau' => PayloadCoercion::string($payload['pau'] ?? ''),
            'dmz' => PayloadCoercion::string($payload['dmz'] ?? ''),
            'chk_dmz' => PayloadCoercion::string($payload['chk_dmz'] ?? ''),
            'permiso' => PayloadCoercion::string($payload['permiso'] ?? ''),
            'txt_sfsv' => PayloadCoercion::string($payload['txt_sfsv'] ?? ''),
            'aOpcionesPau' => NotasFormSupport::desplegableOpciones($payload['aOpcionesPau'] ?? []),
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
    public static function preferenciasFromPayload(array $payload): array
    {
        $zona = $payload['zona_horaria'] ?? null;

        return [
            'layout' => PayloadCoercion::string($payload['layout'] ?? ''),
            'inicio' => PayloadCoercion::string($payload['inicio'] ?? ''),
            'oficina' => PayloadCoercion::string($payload['oficina'] ?? ''),
            'oficinas_posibles' => NotasFormSupport::desplegableOpciones($payload['oficinas_posibles'] ?? []),
            'estilo_azul_selected' => PayloadCoercion::string($payload['estilo_azul_selected'] ?? ''),
            'estilo_naranja_selected' => PayloadCoercion::string($payload['estilo_naranja_selected'] ?? ''),
            'estilo_verde_selected' => PayloadCoercion::string($payload['estilo_verde_selected'] ?? ''),
            'tipo_menu_h' => PayloadCoercion::string($payload['tipo_menu_h'] ?? ''),
            'tipo_menu_v' => PayloadCoercion::string($payload['tipo_menu_v'] ?? ''),
            'tipo_tabla_s' => PayloadCoercion::string($payload['tipo_tabla_s'] ?? ''),
            'tipo_tabla_h' => PayloadCoercion::string($payload['tipo_tabla_h'] ?? ''),
            'tipo_apellidos_ap_nom' => PayloadCoercion::string($payload['tipo_apellidos_ap_nom'] ?? ''),
            'tipo_apellidos_nom_ap' => PayloadCoercion::string($payload['tipo_apellidos_nom_ap'] ?? ''),
            'idioma' => PayloadCoercion::string($payload['idioma'] ?? ''),
            'zona_horaria' => PayloadCoercion::string(empty($zona) ? 'UTC' : $zona),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int|string, string>
     */
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

        return PayloadCoercion::string($id);
    }

    /**
     * @return array<int|string, int>
     */
    public static function permMenuDlMapFromPayload(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            $out[$key] = PayloadCoercion::int($value);
        }

        return $out;
    }

    /**
     * @return array<string, array{cargo: string, email: string}>
     */
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
                'cargo' => PayloadCoercion::string($info['cargo'] ?? ''),
                'email' => PayloadCoercion::string($info['email'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{has_2fa: bool, secret_2fa: string}
     */
    public static function twoFaInfoFromPayload(array $payload): array
    {
        return [
            'has_2fa' => !empty($payload['has_2fa']),
            'secret_2fa' => PayloadCoercion::string($payload['secret_2fa'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $result
     * @return array{
     *     session_auth: array<string, mixed>,
     *     session_config: array<string, mixed>,
     *     esquema: string,
     *     idioma: string
     * }|null
     */
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
            'session_auth' => self::postData($sessionAuth),
            'session_config' => self::postData($sessionConfig),
            'esquema' => PayloadCoercion::string($result['esquema'] ?? ''),
            'idioma' => PayloadCoercion::string($result['idioma'] ?? ''),
        ];
    }

    public static function cambiarIdioma(string $idioma = ''): void
    {
        if ($idioma === '') {
            $sessionAuth = $_SESSION['session_auth'] ?? null;
            if (is_array($sessionAuth) && !empty($sessionAuth['idioma'])) {
                $idioma = PayloadCoercion::string($sessionAuth['idioma']);
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
                $idiomaDefault = SessionConfig::getIdiomaDefault('');
                $idioma = $idiomaDefault !== '' ? $idiomaDefault : 'es_ES.UTF-8';
            }
        }
        // GNU gettext prioriza LANGUAGE sobre LC_*. Debe fijarse siempre.
        // En Docker a menudo no hay locales ca_ES/es_ES instalados: setlocale(idioma)
        // falla y puede dejar LC_MESSAGES=C, con lo que _() deja de traducir aunque
        // LANGUAGE sea correcto. Si falla, caer a un locale que sí exista (C/en_US).
        $domain = 'orbix';
        putenv('LANGUAGE');
        putenv('LC_ALL');
        putenv('LANG');
        putenv("LANGUAGE={$idioma}");
        putenv("LC_ALL={$idioma}");
        putenv("LANG={$idioma}");

        $localeOk = setlocale(LC_ALL, $idioma);
        if ($localeOk === false) {
            $localeOk = setlocale(LC_ALL, str_replace('.UTF-8', '.utf8', $idioma));
        }
        if ($localeOk === false) {
            setlocale(LC_ALL, 'C.UTF-8', 'C.utf8', 'en_US.utf8', 'en_US.UTF-8', 'C');
        }

        bindtextdomain($domain, OrbixRuntime::gettextLanguagesDir());
        bind_textdomain_codeset($domain, 'UTF-8');
        // Forzar recarga del catálogo si el worker ya tenía otro idioma cacheado.
        textdomain('orbix_reset');
        textdomain($domain);
    }

    /**
     * @return array{id_usuario: int, id_role: int}|null
     */
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
            'id_usuario' => PayloadCoercion::int($idUsuario),
            'id_role' => PayloadCoercion::int($idRole),
        ];
    }
}
