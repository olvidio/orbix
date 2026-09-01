<?php

declare(strict_types=1);

namespace src\actividadcargos\application\services;

use src\actividadcargos\domain\CdCargosActivFila;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivWriter;

/**
 * Mantiene al día la copia `cd_cargos_activ_dl` (BD comun) cuando se guarda o
 * elimina un cargo en `d_cargos_activ_dl` (BD sv-e).
 *
 * Sustituye al `copia2Comun()` / `eliminarDeComun()` del código legacy, que se
 * perdió en la migración a repositorios y dejó la copia congelada.
 *
 * No hay transacción entre sv-e y comun: si la copia falla, el cargo de sv-e
 * ya está guardado. El error se registra y lo corrige
 * {@see \src\actividadcargos\application\ResincronizarCdCargosActiv}.
 */
final class SincronizarCdCargosActiv
{
    public function __construct(
        private readonly CdCargosActivWriter $writer,
    ) {
    }

    /**
     * Refleja el estado del cargo en la copia: upsert si debe estar, borrado si no.
     */
    public function sincronizarCargo(ActividadCargo $cargo, ?CdCargosActivContexto $contexto = null): bool
    {
        $contexto ??= CdCargosActivContexto::desdeSesion();
        $fila = CdCargosActivFila::desdeCargo($cargo);

        if (CdCargosActivFila::debeCopiarse($fila)) {
            return $this->writer->upsert($contexto, $fila);
        }

        return $this->writer->eliminar($contexto, CdCargosActivFila::idItem($fila)) !== false;
    }

    /** Cargo eliminado de la tabla origen: fuera también de la copia. */
    public function eliminarCargo(ActividadCargo $cargo, ?CdCargosActivContexto $contexto = null): bool
    {
        $contexto ??= CdCargosActivContexto::desdeSesion();
        $fila = CdCargosActivFila::desdeCargo($cargo);

        return $this->writer->eliminar($contexto, CdCargosActivFila::idItem($fila)) !== false;
    }
}
