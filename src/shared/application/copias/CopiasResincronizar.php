<?php

declare(strict_types=1);

namespace src\shared\application\copias;

use src\actividadcargos\application\ResincronizarCdCargosActiv;
use src\personas\application\ResincronizarCpSacd;
use src\ubis\application\ResincronizarCuCentrosDl;
use src\ubis\application\ResincronizarCuCentrosDlf;
use Throwable;

/**
 * Lanza las reconciliaciones de copias que corresponden a esta instalación.
 *
 * En sv: `cp_sacd`, `cd_cargos_activ_dl` y `cu_centros_dl`.
 * En sf: sólo `cu_centros_dlf` (las otras dos se alimentan desde sv; correrlas
 * aquí vería el origen vacío y borraría la copia).
 */
final class CopiasResincronizar
{
    public function __construct(
        private ResincronizarCpSacd $cpSacd,
        private ResincronizarCdCargosActiv $cdCargos,
        private ResincronizarCuCentrosDl $cuCentrosDl,
        private ResincronizarCuCentrosDlf $cuCentrosDlf,
    ) {
    }

    /**
     * @return array{
     *     tareas: list<array{nombre: string, omitida: ?string, resultado: ?array, error: ?string}>,
     *     errores: int
     * }
     */
    public function execute(bool $aplicar, string $soloEsquema, string $ubicacion): array
    {
        $ubicacion = strtolower(trim($ubicacion));
        $tareas = [];
        $errores = 0;

        foreach (self::planPara($ubicacion) as $paso) {
            if ($paso['omitida'] !== null || $paso['clave'] === null) {
                $tareas[] = [
                    'nombre' => $paso['nombre'],
                    'omitida' => $paso['omitida'],
                    'resultado' => null,
                    'error' => null,
                ];
                continue;
            }
            try {
                $resultado = $this->runner($paso['clave'])->execute($aplicar, $soloEsquema);
                $tareas[] = [
                    'nombre' => $paso['nombre'],
                    'omitida' => null,
                    'resultado' => $resultado,
                    'error' => null,
                ];
                $errores += (int) ($resultado['totales']['errores'] ?? 0);
            } catch (Throwable $e) {
                $errores++;
                $tareas[] = [
                    'nombre' => $paso['nombre'],
                    'omitida' => null,
                    'resultado' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return ['tareas' => $tareas, 'errores' => $errores];
    }

    /**
     * Qué copias corren en esta instalación. `clave` es null si se omite.
     *
     * @return list<array{nombre: string, omitida: ?string, clave: ?string}>
     */
    public static function planPara(string $ubicacion): array
    {
        $ubicacion = strtolower(trim($ubicacion));
        $omitirSv = $ubicacion === 'sv'
            ? null
            : sprintf('sólo se ejecuta desde sv (UBICACION=%s)', $ubicacion ?: '?');

        $centrosClave = match ($ubicacion) {
            'sv' => 'cu_centros_dl',
            'sf' => 'cu_centros_dlf',
            default => null,
        };

        return [
            [
                'nombre' => 'cp_sacd',
                'omitida' => $omitirSv,
                'clave' => $omitirSv === null ? 'cp_sacd' : null,
            ],
            [
                'nombre' => 'cd_cargos_activ_dl',
                'omitida' => $omitirSv,
                'clave' => $omitirSv === null ? 'cd_cargos_activ_dl' : null,
            ],
            [
                'nombre' => $centrosClave ?? 'cu_centros_dl',
                'omitida' => $centrosClave === null
                    ? sprintf('sólo se ejecuta desde sv o sf (UBICACION=%s)', $ubicacion ?: '?')
                    : null,
                'clave' => $centrosClave,
            ],
        ];
    }

    private function runner(string $clave): ReconciliadorCopia
    {
        return match ($clave) {
            'cp_sacd' => $this->cpSacd,
            'cd_cargos_activ_dl' => $this->cdCargos,
            'cu_centros_dl' => $this->cuCentrosDl,
            'cu_centros_dlf' => $this->cuCentrosDlf,
            default => throw new \InvalidArgumentException('copia desconocida: ' . $clave),
        };
    }
}
