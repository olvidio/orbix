<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql;

use PDOException;
use src\personas\domain\contracts\PersonaDePasoDatosRepositoryInterface as Datos;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;

/**
 * Busca datos de una persona de paso en las tablas madre, que por herencia incluyen las de todas las dl
 * (PostgreSQL comprueba los permisos solo sobre la madre). `{p}` es `publicv` o `publicf` según la sección:
 *
 *   - asistencias: `resto{s}.d_asistentes_ex` (`{s}` es `v` o `f`), que es donde se graban las de las
 *     personas de paso. No se leen las madres (`global.d_asistentes_dl`, `{p}.d_asistentes_all`): en
 *     producción las dl no pueden. Las asistencias de sacd de paso anteriores al
 *     2026-11-18 quedaron en `d_asistentes_dl` y aquí no se ven; ver `docs/dev/backlog.md`.
 *   - cargos: `global.d_cargos_activ`; encargos: `global.encargos_sacd` (solo sv)
 *   - notas: `{p}.e_notas`; matrículas: `{p}.d_matriculas_activ`; docencia: `global.d_docencia_stgr`
 *   - zonas de sacd: `global.zonas_sacd` (comun)
 *
 * Una tabla que no existe en la sección (encargos en sf) se salta.
 */
final class PgPersonaDePasoDatosRepository implements Datos
{
    /** @var list<array{0: string, 1: string, 2: string}> tipo, conexión global, tabla */
    private const ORIGENES = [
        [Datos::ASISTENCIAS, 'oDBE_Select', 'resto{s}.d_asistentes_ex'],
        [Datos::CARGOS, 'oDBE_Select', 'global.d_cargos_activ'],
        [Datos::ENCARGOS, 'oDBE_Select', 'global.encargos_sacd'],
        [Datos::NOTAS, 'oDB', '{p}.e_notas'],
        [Datos::MATRICULAS, 'oDB', '{p}.d_matriculas_activ'],
        [Datos::DOCENCIA, 'oDB', 'global.d_docencia_stgr'],
        [Datos::ZONAS, 'oDBC_Select', 'global.zonas_sacd'],
    ];

    /** SQLSTATE de «la relación no existe». */
    private const TABLA_INEXISTENTE = '42P01';

    public function datosDe(int $idNom): array
    {
        $seccion = ConfigGlobal::mi_sfsv() === 2 ? 'f' : 'v';
        $datos = [];
        foreach (self::ORIGENES as [$tipo, $conexion, $tabla]) {
            if (in_array($tipo, $datos, true)) {
                continue;
            }
            $tabla = str_replace(['{p}', '{s}'], ['public' . $seccion, $seccion], $tabla);
            try {
                $st = GlobalPdo::get($conexion)->prepare("SELECT 1 FROM $tabla WHERE id_nom = :id LIMIT 1");
                $st->execute([':id' => $idNom]);
            } catch (PDOException $e) {
                if ((string) ($e->errorInfo[0] ?? '') === self::TABLA_INEXISTENTE) {
                    continue;
                }
                // Permisos u otro fallo: se dice en qué tabla, para poder diagnosticarlo.
                throw new \RuntimeException($tabla . ': ' . ($e->errorInfo[2] ?? $e->getMessage()), 0, $e);
            }
            if ($st->fetchColumn() !== false) {
                $datos[] = $tipo;
            }
        }

        return $datos;
    }
}
