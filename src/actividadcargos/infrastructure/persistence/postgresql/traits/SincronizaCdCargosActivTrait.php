<?php

declare(strict_types=1);

namespace src\actividadcargos\infrastructure\persistence\postgresql\traits;

use src\actividadcargos\application\services\SincronizarCdCargosActiv;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use Throwable;

/**
 * Engancha el mantenimiento de la copia `cd_cargos_activ_dl` al
 * guardado/borrado de `d_cargos_activ_dl`.
 *
 * Un fallo aquí nunca debe tumbar el guardado del cargo: se registra en
 * `log/cd_cargos_activ.err` y la reconciliación periódica lo arregla.
 */
trait SincronizaCdCargosActivTrait
{
    protected function sincronizarCdCargosActiv(ActividadCargo $cargo): void
    {
        $this->ejecutarSincronizacionCdCargosActiv(
            static fn(SincronizarCdCargosActiv $servicio): bool => $servicio->sincronizarCargo($cargo),
            'sincronizar',
            $cargo,
        );
    }

    protected function eliminarDeCdCargosActiv(ActividadCargo $cargo): void
    {
        $this->ejecutarSincronizacionCdCargosActiv(
            static fn(SincronizarCdCargosActiv $servicio): bool => $servicio->eliminarCargo($cargo),
            'eliminar',
            $cargo,
        );
    }

    /** @param callable(SincronizarCdCargosActiv): bool $accion */
    private function ejecutarSincronizacionCdCargosActiv(callable $accion, string $nombre, ActividadCargo $cargo): void
    {
        try {
            $servicio = DependencyResolver::get(SincronizarCdCargosActiv::class);
            if ($accion($servicio) === false) {
                $this->logCdCargosActiv(sprintf('%s: la copia devolvió false (%s)', $nombre, $this->idItemDe($cargo)));
            }
        } catch (Throwable $e) {
            $this->logCdCargosActiv(sprintf('%s: %s (%s)', $nombre, $e->getMessage(), $this->idItemDe($cargo)));
        }
    }

    private function idItemDe(ActividadCargo $cargo): string
    {
        return 'id_item=' . (string) $cargo->getId_item();
    }

    private function logCdCargosActiv(string $mensaje): void
    {
        $line = sprintf("[%s] cd_cargos_activ_dl: %s\n", date('c'), $mensaje);
        error_log($line, 3, ConfigGlobal::$directorio . '/log/cd_cargos_activ.err');
    }
}
