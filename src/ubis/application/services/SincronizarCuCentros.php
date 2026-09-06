<?php

declare(strict_types=1);

namespace src\ubis\application\services;

use src\shared\infrastructure\persistence\copias\CopiaWriter;
use src\ubis\domain\CuCentrosFila;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosContexto;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlfWriter;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlWriter;

/**
 * Mantiene al día las copias de centros de la BD comun (`cu_centros_dl` y
 * `cu_centros_dlf`) cuando se guarda o elimina un centro en `u_centros_dl`.
 *
 * Cada instalación es dueña de sus centros: la sv guarda los de sv (y alimenta
 * `cu_centros_dl`), la sf los de sf (y alimenta `cu_centros_dlf`). El destino se
 * decide por el primer dígito de `id_ubi`, no por la instalación, que es la
 * misma regla que usan `DBTrasvase::ctr` y `UbiFactory`.
 *
 * Antes de esto la copia sólo se escribía en el trasvase inicial: `CentrosUpdate`
 * guardaba en `u_centros_dl` y no propagaba nada, así que las copias quedaban
 * congeladas desde el alta de la dl.
 *
 * No hay transacción entre la base de origen y comun: si la copia falla, el
 * centro ya está guardado. El error se registra y lo corrige la reconciliación
 * ({@see \src\ubis\application\ResincronizarCuCentrosDl} y su equivalente sf).
 */
final class SincronizarCuCentros
{
    public function __construct(
        private readonly CuCentrosDlWriter $writerSv,
        private readonly CuCentrosDlfWriter $writerSf,
    ) {
    }

    /** Refleja el estado del centro en la copia que le corresponde. */
    public function sincronizarCentro(object $centro): bool
    {
        $fila = CuCentrosFila::desdeCentro($centro);
        $id_ubi = CuCentrosFila::idUbi($fila);
        if ($id_ubi === 0) {
            return false;
        }

        return $this->writerDe($id_ubi)->upsert(CuCentrosContexto::desdeSesionPara($id_ubi), $fila);
    }

    /** Centro eliminado de la tabla origen: fuera también de la copia. */
    public function eliminarCentro(object $centro): bool
    {
        $fila = CuCentrosFila::desdeCentro($centro);
        $id_ubi = CuCentrosFila::idUbi($fila);
        if ($id_ubi === 0) {
            return false;
        }

        return $this->writerDe($id_ubi)->eliminar(CuCentrosContexto::desdeSesionPara($id_ubi), $id_ubi);
    }

    private function writerDe(int $id_ubi): CopiaWriter
    {
        return CuCentrosFila::esDeSf($id_ubi) ? $this->writerSf : $this->writerSv;
    }
}
