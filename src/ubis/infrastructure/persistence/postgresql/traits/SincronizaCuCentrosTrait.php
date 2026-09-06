<?php

declare(strict_types=1);

namespace src\ubis\infrastructure\persistence\postgresql\traits;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\services\SincronizarCuCentros;
use Throwable;

/**
 * Engancha el mantenimiento de las copias de centros al guardado/borrado de
 * `u_centros_dl`.
 *
 * Se resuelve el servicio por el contenedor en vez de inyectarlo por
 * constructor porque el repositorio se construye sin argumentos en varios
 * sitios; así el enganche no cambia su firma.
 *
 * Un fallo aquí nunca debe tumbar el guardado del centro: se registra en
 * `log/cu_centros.err` y la reconciliación periódica lo arregla.
 */
trait SincronizaCuCentrosTrait
{
    /** La conexión del repositorio de origen; la pone `ClaseRepository`. */
    abstract public function getoDbl(): PDO;

    protected function sincronizarCuCentros(object $centro): void
    {
        $this->ejecutarSincronizacionCuCentros(
            static fn(SincronizarCuCentros $servicio): bool => $servicio->sincronizarCentro($centro),
            'sincronizar',
            $centro,
        );
    }

    protected function eliminarDeCuCentros(object $centro): void
    {
        $this->ejecutarSincronizacionCuCentros(
            static fn(SincronizarCuCentros $servicio): bool => $servicio->eliminarCentro($centro),
            'eliminar',
            $centro,
        );
    }

    /** @param callable(SincronizarCuCentros): bool $accion */
    private function ejecutarSincronizacionCuCentros(callable $accion, string $nombre, object $centro): void
    {
        if (!$this->sincronizacionCuCentrosCorresponde()) {
            return;
        }

        try {
            $servicio = DependencyResolver::get(SincronizarCuCentros::class);
            if ($accion($servicio) === false) {
                $this->logCuCentros(sprintf('%s: la copia devolvió false (%s)', $nombre, $this->idUbiDe($centro)));
            }
        } catch (Throwable $e) {
            $this->logCuCentros(sprintf('%s: %s (%s)', $nombre, $e->getMessage(), $this->idUbiDe($centro)));
        }
    }

    /**
     * El trasvase de una dl nueva (`DBTrasvase::ctr`) reapunta la conexión del
     * repositorio al esquema destino y escribe las copias él mismo, sobre la
     * conexión de comun de ese destino. La sesión de quien lo ejecuta es otra, de
     * modo que sincronizar aquí metería los centros en la copia del esquema
     * equivocado: si la conexión ya no es la de la sesión, esto no es cosa nuestra.
     */
    private function sincronizacionCuCentrosCorresponde(): bool
    {
        return $this->getoDbl() === ($GLOBALS['oDB'] ?? null);
    }

    private function idUbiDe(object $centro): string
    {
        if (method_exists($centro, 'getId_ubi')) {
            return 'id_ubi=' . (string) $centro->getId_ubi();
        }

        return get_class($centro);
    }

    private function logCuCentros(string $mensaje): void
    {
        $line = sprintf("[%s] cu_centros: %s\n", date('c'), $mensaje);
        error_log($line, 3, ConfigGlobal::$directorio . '/log/cu_centros.err');
    }
}
