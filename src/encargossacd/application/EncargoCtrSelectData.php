<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\value_objects\EncargoGrupo;

/**
 * Payload JSON para el desplegable de centros segun filtro (y zona opcional).
 * Devuelve el contrato estandar definido en `refactor.md`
 * (`id`, `name`, `opciones`, `selected`, `blanco`, `val_blanco`, `action`)
 * para que el frontend monte el `<select>` con `fnjs_construir_desplegable`
 * (o el modelo `frontend/encargossacd/model/DesplCentros`).
 *
 * Importante: esta clase vive en capa `application` y por tanto **no** puede
 * instanciar `web\Desplegable` (ver `refactor.md`).
 */
final class EncargoCtrSelectData
{
    /**
     * @return array{id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public static function execute(int $id_ubi, int $filtro_ctr, int $id_zona): array
    {
        $filtro_eff = $id_zona !== 0 ? EncargoGrupo::ZONAS_MISAS : $filtro_ctr;

        $opciones_raw = CentrosPorFiltroOpciones::getOpciones($filtro_eff, $id_zona);
        $opciones = [];
        foreach ($opciones_raw as $k => $v) {
            $opciones[(string)$k] = (string)$v;
        }

        $blanco = $filtro_eff === EncargoGrupo::CGI
            || ($filtro_eff === EncargoGrupo::ZONAS_MISAS && $id_zona !== 0);

        return [
            'id' => 'lst_ctrs',
            'name' => 'lst_ctrs',
            'opciones' => $opciones,
            'selected' => (string)$id_ubi,
            'blanco' => $blanco,
            'val_blanco' => '',
            'action' => 'fnjs_ver_ficha()',
        ];
    }
}
