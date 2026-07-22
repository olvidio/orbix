<?php

namespace src\encargossacd\application;

use src\encargossacd\application\CentrosPorFiltroOpciones;

use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\ubis\domain\entity\Ubi;

/**
 * Payload JSON para el desplegable de centros segun filtro (y zona opcional).
 * Devuelve el contrato estandar definido en `refactor.md`
 * (`id`, `name`, `opciones`, `selected`, `blanco`, `val_blanco`, `action`)
 * para que el frontend monte el `<select>` con `fnjs_construir_desplegable`
 * (o el modelo `frontend/encargossacd/model/DesplCentros`).
 *
 * Importante: esta clase vive en capa `application` y por tanto **no** puede
 * instanciar `frontend\shared\web\Desplegable` (ver `refactor.md`).
 */
final class EncargoCtrSelectData
{

    public function __construct(
        private CentrosPorFiltroOpciones $centrosPorFiltroOpciones
    ) {
    }

    /**
     * @param string|null $action Handler `onchange` del `<select>`. Si es `null`
     *     se usa el de la ficha de centro (`fnjs_ver_ficha()`), pensado para
     *     `ctr_ficha`. Una vista sin ficha (p.ej. `encargo_ver`) pasa `''` para
     *     no vincular ningún `onchange`.
     * @return array{id: string, name: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(int $id_ubi, int $filtro_ctr, int $id_zona, ?string $action = null): array
    {
        [$filtro_eff, $id_zona_eff] = $this->resolverFiltro($filtro_ctr, $id_zona);

        $opciones_raw = $this->centrosPorFiltroOpciones->getOpciones($filtro_eff, $id_zona_eff);
        $opciones_raw = $this->opcionesConCentroSeleccionado($opciones_raw, $id_ubi);

        $blanco = $id_ubi === 0
            || $filtro_eff === EncargoGrupo::CGI
            || ($filtro_eff === EncargoGrupo::ZONAS_MISAS && $id_zona_eff !== 0);

        return [
            'id' => 'lst_ctrs',
            'name' => 'lst_ctrs',
            'opciones' => OpcionesDesplegable::enOrden($opciones_raw),
            'selected' => $id_ubi === 0 ? '' : (string)$id_ubi,
            'blanco' => $blanco,
            'val_blanco' => '',
            'action' => $action ?? 'fnjs_ver_ficha()',
        ];
    }

    /**
     * @return array{0: int, 1: int} [filtro_eff, id_zona_eff]
     */
    private function resolverFiltro(int $filtro_ctr, int $id_zona): array
    {
        if ($filtro_ctr === EncargoGrupo::ZONAS_MISAS) {
            return [EncargoGrupo::ZONAS_MISAS, $id_zona];
        }
        // AJAX de zona en encargo_ver puede enviar solo id_zona (filtro_ctr=0).
        if ($filtro_ctr === 0 && $id_zona !== 0) {
            return [EncargoGrupo::ZONAS_MISAS, $id_zona];
        }

        return [$filtro_ctr, 0];
    }

    /**
     * @param array<string, string> $opciones_raw
     * @return array<string, string>
     */
    private function opcionesConCentroSeleccionado(array $opciones_raw, int $id_ubi): array
    {
        if ($id_ubi !== 0 && !array_key_exists((string)$id_ubi, $opciones_raw)) {
            $oUbi = Ubi::NewUbi($id_ubi);
            if ($oUbi !== null) {
                $opciones_raw[(string)$id_ubi] = $oUbi->getNombre_ubi();
            }
        }

        $normalizadas = [];
        foreach ($opciones_raw as $id => $nombre) {
            $normalizadas[(string) $id] = $nombre;
        }

        return $normalizadas;
    }
}
