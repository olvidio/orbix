<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFrontSignedLink;
use src\configuracion\domain\value_objects\ConfigSnapshot;

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

    public static function oConfig(): ?ConfigSnapshot
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
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
                $out[$k] = PayloadCoercion::string($v);
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
            'id_asignatura' => PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'id_nivel' => PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'nombre_asignatura' => PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
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
            'id_nivel_asig' => PayloadCoercion::int($raw['id_nivel_asig'] ?? 0),
            'id_nivel' => PayloadCoercion::int($raw['id_nivel'] ?? 0),
            'id_asignatura' => PayloadCoercion::int($raw['id_asignatura'] ?? 0),
            'nombre_asignatura' => PayloadCoercion::string($raw['nombre_asignatura'] ?? ''),
            'creditos' => PayloadCoercion::string($raw['creditos'] ?? ''),
            'nota_txt' => PayloadCoercion::string($raw['nota_txt'] ?? ''),
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
            'id_nom' => PayloadCoercion::int($payload['id_nom'] ?? 0),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'idioma' => PayloadCoercion::string($payload['idioma'] ?? ''),
            'destino' => PayloadCoercion::string($payload['destino'] ?? ''),
            'certificado' => PayloadCoercion::string($payload['certificado'] ?? ''),
            'f_certificado' => PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'f_enviado' => PayloadCoercion::string($payload['f_enviado'] ?? ''),
            'firmado' => $payload['firmado'] ?? false,
            'content' => $content,
            'apellidos_nombre' => PayloadCoercion::string($payload['apellidos_nombre'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{aviso: string, nom: string, f_enviado: string}
     */
    public static function adjuntarFormFromPayload(array $payload): array
    {
        return [
            'aviso' => PayloadCoercion::string($payload['aviso'] ?? ''),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'f_enviado' => PayloadCoercion::string($payload['f_enviado'] ?? ''),
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
            'id_nom' => PayloadCoercion::int($payload['id_nom'] ?? 0),
            'id_item' => PayloadCoercion::int($payload['id_item'] ?? 0),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'idioma' => PayloadCoercion::string($payload['idioma'] ?? ''),
            'destino' => PayloadCoercion::string($payload['destino'] ?? ''),
            'certificado' => PayloadCoercion::string($payload['certificado'] ?? ''),
            'f_certificado' => PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'f_recibido' => PayloadCoercion::string($payload['f_recibido'] ?? ''),
            'chk_firmado' => PayloadCoercion::string($payload['chk_firmado'] ?? ''),
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
            'id_item' => PayloadCoercion::int($payload['id_item'] ?? 0),
            'id_nom' => PayloadCoercion::int($payload['id_nom'] ?? 0),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'apellidos_nombre' => PayloadCoercion::string($payload['apellidos_nombre'] ?? ''),
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
            'nombreApellidos' => PayloadCoercion::string($payload['nombreApellidos'] ?? ''),
            'lugar_nacimiento' => PayloadCoercion::string($payload['lugar_nacimiento'] ?? ''),
            'f_nacimiento' => PayloadCoercion::string($payload['f_nacimiento'] ?? ''),
            'nivel_stgr' => $payload['nivel_stgr'] ?? null,
            'region_latin' => PayloadCoercion::string($payload['region_latin'] ?? ''),
            'vstgr' => PayloadCoercion::string($payload['vstgr'] ?? ''),
            'dir_stgr' => PayloadCoercion::string($payload['dir_stgr'] ?? ''),
            'lugar_firma' => PayloadCoercion::string($payload['lugar_firma'] ?? ''),
            'contador' => PayloadCoercion::string($payload['contador'] ?? ''),
            'f_certificado' => PayloadCoercion::string($payload['f_certificado'] ?? ''),
            'any' => PayloadCoercion::string($payload['any_2digit'] ?? ''),
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
