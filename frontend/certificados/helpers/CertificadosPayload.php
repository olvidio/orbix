<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\session\SessionConfig;

final class CertificadosPayload
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
     * @return array<string, mixed>
     */
    public static function hashCamposHidden(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $k => $v) {
            if (is_string($k)) {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    public static function oConfig(): bool
    {
        return SessionConfig::isPresent();
    }

    /**
     * @return array{path: string, query?: array<string, mixed>}|null
     */
    public static function linkSpec(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }
        $path = $raw['path'] ?? null;
        if (!is_string($path) || $path === '') {
            return null;
        }
        $spec = ['path' => $path];
        $query = $raw['query'] ?? null;
        if (is_array($query)) {
            $q = [];
            foreach ($query as $k => $v) {
                $q[(string) $k] = $v;
            }
            if ($q !== []) {
                $spec['query'] = $q;
            }
        }

        return $spec;
    }

    /**
     * @return array<string, string>
     */
    public static function latinReplaceMap(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $k => $v) {
            if (is_string($k)) {
                $out[$k] = \frontend\shared\helpers\PayloadCoercion::string($v);
            }
        }

        return $out;
    }

    /**
     * @return array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}
     */
    public static function asignaturaRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['id_asignatura' => 0, 'id_nivel' => 0, 'nombre_asignatura' => '', 'creditos' => 0.0];
        }

        return [
            'id_asignatura' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'id_nivel' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'nombre_asignatura' => \frontend\shared\helpers\PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
            'creditos' => self::creditosFloat($raw['creditos'] ?? 0),
        ];
    }

    /**
     * @return list<array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}>
     */
    public static function asignaturasFromJson(mixed $raw): array
    {
        if (!is_string($raw) || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw);
        if (!is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $item) {
            $rowRaw = null;
            if (is_string($item)) {
                $rowRaw = json_decode($item, true);
            } elseif (is_array($item)) {
                $rowRaw = $item;
            } elseif (is_object($item)) {
                $rowRaw = (array) $item;
            }
            $out[] = self::asignaturaRow($rowRaw);
        }

        return $out;
    }

    /**
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
     */
    public static function aprobadaRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [
                'id_nivel_asig' => 0,
                'id_nivel' => 0,
                'id_asignatura' => 0,
                'nombre_asignatura' => '',
                'creditos' => '',
                'nota_txt' => '',
            ];
        }

        return [
            'id_nivel_asig' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel_asig'] ?? 0),
            'id_nivel' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'id_asignatura' => \frontend\shared\helpers\PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'nombre_asignatura' => \frontend\shared\helpers\PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
            'creditos' => \frontend\shared\helpers\PayloadCoercion::string($raw['creditos'] ?? ''),
            'nota_txt' => \frontend\shared\helpers\PayloadCoercion::string($raw['nota_txt'] ?? ''),
        ];
    }

    /**
     * @return array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}>
     */
    public static function aprobadasFromPayload(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $item) {
            if (is_int($key)) {
                $out[$key] = self::aprobadaRow($item);
            } elseif (is_numeric($key)) {
                $out[(int) $key] = self::aprobadaRow($item);
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     cabeceras: list<array<string, mixed>|string>,
     *     botones: list<array<string, mixed>>,
     *     valores: array<int|string, mixed>,
     * }
     */
    public static function emitidoListaTablaFromPayload(array $payload): array
    {
        return [
            'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
            'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     id_nom: int,
     *     nom: string,
     *     idioma: string,
     *     destino: string,
     *     certificado: string,
     *     f_certificado: string,
     *     f_enviado: string,
     *     firmado: mixed,
     *     content: string,
     *     apellidos_nombre: string,
     * }
     */
    public static function emitidoVerFromPayload(array $payload): array
    {
        $contentRaw = $payload['content'] ?? '';
        $content = is_string($contentRaw) ? $contentRaw : '';

        return [
            'id_nom' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_nom'] ?? 0),
            'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
            'idioma' => \frontend\shared\helpers\PayloadCoercion::string($payload['idioma'] ?? ''),
            'destino' => \frontend\shared\helpers\PayloadCoercion::string($payload['destino'] ?? ''),
            'certificado' => \frontend\shared\helpers\PayloadCoercion::string($payload['certificado'] ?? ''),
            'f_certificado' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'f_enviado' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_enviado'] ?? ''),
            'firmado' => $payload['firmado'] ?? false,
            'content' => $content,
            'apellidos_nombre' => \frontend\shared\helpers\PayloadCoercion::string($payload['apellidos_nombre'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{aviso: string, nom: string, f_enviado: string}
     */
    public static function adjuntarFormFromPayload(array $payload): array
    {
        return [
            'aviso' => \frontend\shared\helpers\PayloadCoercion::string($payload['aviso'] ?? ''),
            'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
            'f_enviado' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_enviado'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     id_nom: int,
     *     id_item: int,
     *     nom: string,
     *     idioma: string,
     *     destino: string,
     *     certificado: string,
     *     f_certificado: string,
     *     f_recibido: string,
     *     chk_firmado: string,
     *     a_locales: array<int|string, string>,
     * }
     */
    public static function recibidoFormFromPayload(array $payload): array
    {
        return [
            'id_nom' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_nom'] ?? 0),
            'id_item' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_item'] ?? 0),
            'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
            'idioma' => \frontend\shared\helpers\PayloadCoercion::string($payload['idioma'] ?? ''),
            'destino' => \frontend\shared\helpers\PayloadCoercion::string($payload['destino'] ?? ''),
            'certificado' => \frontend\shared\helpers\PayloadCoercion::string($payload['certificado'] ?? ''),
            'f_certificado' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'f_recibido' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_recibido'] ?? ''),
            'chk_firmado' => \frontend\shared\helpers\PayloadCoercion::string($payload['chk_firmado'] ?? ''),
            'a_locales' => NotasFormSupport::desplegableOpciones($payload['a_locales'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     id_item: int,
     *     id_nom: int,
     *     nom: string,
     *     apellidos_nombre: string,
     * }
     */
    public static function uploadFirmadoFromPayload(array $payload): array
    {
        return [
            'id_item' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_item'] ?? 0),
            'id_nom' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_nom'] ?? 0),
            'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
            'apellidos_nombre' => \frontend\shared\helpers\PayloadCoercion::string($payload['apellidos_nombre'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     nombreApellidos: string,
     *     lugar_nacimiento: string,
     *     f_nacimiento: string,
     *     nivel_stgr: mixed,
     *     region_latin: string,
     *     vstgr: string,
     *     dir_stgr: string,
     *     lugar_firma: string,
     *     contador: string,
     *     f_certificado: string,
     *     any: string,
     * }
     */
    public static function imprimirPersonaFromPayload(array $payload): array
    {
        return [
            'nombreApellidos' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombreApellidos'] ?? ''),
            'lugar_nacimiento' => \frontend\shared\helpers\PayloadCoercion::string($payload['lugar_nacimiento'] ?? ''),
            'f_nacimiento' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
            'nivel_stgr' => $payload['nivel_stgr'] ?? null,
            'region_latin' => \frontend\shared\helpers\PayloadCoercion::string($payload['region_latin'] ?? ''),
            'vstgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['vstgr'] ?? ''),
            'dir_stgr' => \frontend\shared\helpers\PayloadCoercion::string($payload['dir_stgr'] ?? ''),
            'lugar_firma' => \frontend\shared\helpers\PayloadCoercion::string($payload['lugar_firma'] ?? ''),
            'contador' => \frontend\shared\helpers\PayloadCoercion::string($payload['contador'] ?? ''),
            'f_certificado' => \frontend\shared\helpers\PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'any' => \frontend\shared\helpers\PayloadCoercion::string($payload['any_2digit'] ?? ''),
        ];
    }

    public static function urlNuevoFromSpec(mixed $spec): string
    {
        return HashFrontSignedLink::tryFromSpec($spec);
    }

    private static function creditosFloat(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return 0.0;
    }
}
