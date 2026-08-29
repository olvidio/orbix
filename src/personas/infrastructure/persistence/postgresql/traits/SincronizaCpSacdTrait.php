<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql\traits;

use src\personas\application\services\SincronizarCpSacd;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use Throwable;

/**
 * Engancha el mantenimiento de la copia `cp_sacd` al guardado/borrado de las
 * tablas que la alimentan (`p_numerarios`, `p_agregados`, `p_sssc`,
 * `p_de_paso_ex`).
 *
 * Se resuelve el servicio por el contenedor en vez de inyectarlo por
 * constructor porque estos repositorios se construyen sin argumentos en varios
 * sitios; así el enganche no cambia sus firmas.
 *
 * Un fallo aquí nunca debe tumbar el guardado de la ficha: se registra en
 * `log/cp_sacd.err` y la reconciliación periódica lo arregla.
 */
trait SincronizaCpSacdTrait
{
    protected function sincronizarCpSacd(object $persona): void
    {
        $this->ejecutarSincronizacionCpSacd(
            static fn(SincronizarCpSacd $servicio): bool => $servicio->sincronizarPersona($persona),
            'sincronizar',
            $persona,
        );
    }

    protected function eliminarDeCpSacd(object $persona): void
    {
        $this->ejecutarSincronizacionCpSacd(
            static fn(SincronizarCpSacd $servicio): bool => $servicio->eliminarPersona($persona),
            'eliminar',
            $persona,
        );
    }

    /** @param callable(SincronizarCpSacd): bool $accion */
    private function ejecutarSincronizacionCpSacd(callable $accion, string $nombre, object $persona): void
    {
        try {
            $servicio = DependencyResolver::get(SincronizarCpSacd::class);
            if ($accion($servicio) === false) {
                $this->logCpSacd(sprintf('%s: la copia devolvió false (%s)', $nombre, $this->idNomDe($persona)));
            }
        } catch (Throwable $e) {
            $this->logCpSacd(sprintf('%s: %s (%s)', $nombre, $e->getMessage(), $this->idNomDe($persona)));
        }
    }

    private function idNomDe(object $persona): string
    {
        if (method_exists($persona, 'getId_nom')) {
            return 'id_nom=' . (string) $persona->getId_nom();
        }

        return get_class($persona);
    }

    private function logCpSacd(string $mensaje): void
    {
        $line = sprintf("[%s] cp_sacd: %s\n", date('c'), $mensaje);
        error_log($line, 3, ConfigGlobal::$directorio . '/log/cp_sacd.err');
    }
}
