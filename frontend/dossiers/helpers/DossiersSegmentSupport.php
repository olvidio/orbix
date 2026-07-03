<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFrontSignedLink;

final class DossiersSegmentSupport
{
    /**
     * @param array<int|string, mixed> $seg
     * @return array{
     *     titulo: string,
     *     action_tabla_url: string,
     *     ins_traslado_url: string,
     *     hash_campos_form: string,
     *     hash_campos_no: string,
     *     hash_campos_hidden: array<string, mixed>,
     *     tabla_id: string,
     *     tabla_cabeceras: list<array<string, mixed>|string>,
     *     tabla_botones: list<array<string, mixed>>,
     *     tabla_valores: array<int|string, mixed>,
     *     permiso: int,
     *     script_ctx: array{bloque: string, action_form: string, action_update: string, eliminar_txt: string},
     * }
     */
    public static function datosTablaFromSegment(array $seg): array
    {
        $tablaRaw = $seg['tabla'] ?? [];
        $tabla = is_array($tablaRaw) ? $tablaRaw : [];
        $hashRaw = $seg['hash'] ?? [];
        $hash = is_array($hashRaw) ? $hashRaw : [];
        $hiddenRaw = $hash['campos_hidden'] ?? [];
        $hidden = [];
        if (is_array($hiddenRaw)) {
            foreach ($hiddenRaw as $k => $v) {
                if (is_string($k)) {
                    $hidden[$k] = $v;
                }
            }
        }
        $scriptCtxRaw = $seg['script_ctx'] ?? [];
        $scriptCtx = is_array($scriptCtxRaw) ? $scriptCtxRaw : [];

        return [
            'titulo' => PayloadCoercion::string($seg['titulo'] ?? ''),
            'action_tabla_url' => HashFrontSignedLink::tryFromSpec($seg['action_tabla_link_spec'] ?? null),
            'ins_traslado_url' => HashFrontSignedLink::tryFromSpec($seg['ins_traslado_link_spec'] ?? null),
            'hash_campos_form' => PayloadCoercion::string($hash['campos_form'] ?? 'mod'),
            'hash_campos_no' => PayloadCoercion::string($hash['campos_no'] ?? ''),
            'hash_campos_hidden' => $hidden,
            'tabla_id' => PayloadCoercion::string($tabla['id_tabla'] ?? 'datos_sql'),
            'tabla_cabeceras' => ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []),
            'tabla_botones' => ActividadesListaSupport::botones($tabla['botones'] ?? []),
            'tabla_valores' => ActividadesListaSupport::datos($tabla['valores'] ?? []),
            'permiso' => PayloadCoercion::int($seg['permiso'] ?? 0),
            'script_ctx' => [
                'bloque' => PayloadCoercion::string($scriptCtx['bloque'] ?? ''),
                'action_form' => PayloadCoercion::string($scriptCtx['action_form'] ?? ''),
                'action_update' => PayloadCoercion::string($scriptCtx['action_update'] ?? ''),
                'eliminar_txt' => PayloadCoercion::string($scriptCtx['eliminar_txt'] ?? ''),
            ],
        ];
    }
}
