<?php

declare(strict_types=1);

namespace src\personas\application\services;

use src\personas\domain\CpSacdFila;
use src\personas\infrastructure\persistence\postgresql\CpSacdContexto;
use src\personas\infrastructure\persistence\postgresql\CpSacdWriter;

/**
 * Mantiene al día la copia `cp_sacd` (BD comun) cuando se guarda o elimina una
 * persona en la BD interior.
 *
 * Sustituye al `copia2Comun()` / `eliminarDeComun()` del código legacy
 * (`apps/personas/model/entity/PersonaDl.php:256` y `PersonaPub.php:274`), que
 * se perdió en la migración a repositorios y dejó la copia congelada. A
 * diferencia del legacy:
 *   - borra de la copia cuando la persona deja de ser sacd, se traslada o se
 *     elimina (el legacy sólo lo hacía con los de paso, de ahí los huérfanos);
 *   - no vive en los setters de la entidad, sino en el guardado del repositorio.
 *
 * No hay transacción entre la BD interior y comun: si la copia falla, la ficha
 * ya está guardada. El error se registra y lo corrige
 * {@see \src\personas\application\ResincronizarCpSacd}.
 */
final class SincronizarCpSacd
{
    public function __construct(
        private readonly CpSacdWriter $writer,
    ) {
    }

    /**
     * Refleja el estado de la persona en la copia: upsert si debe estar,
     * borrado si no (dejó de ser sacd, o un de paso que ya no está en la dl).
     */
    public function sincronizarPersona(object $persona, ?CpSacdContexto $contexto = null): bool
    {
        $contexto ??= CpSacdContexto::desdeSesion();
        $fila = CpSacdFila::desdePersona($persona);

        if (CpSacdFila::debeCopiarse($fila, $contexto->dl)) {
            return $this->writer->upsert($contexto, $fila);
        }

        return $this->writer->eliminar($contexto, CpSacdFila::idNom($fila)) !== false;
    }

    /** Persona eliminada de la tabla origen: fuera también de la copia. */
    public function eliminarPersona(object $persona, ?CpSacdContexto $contexto = null): bool
    {
        $contexto ??= CpSacdContexto::desdeSesion();
        $fila = CpSacdFila::desdePersona($persona);

        return $this->writer->eliminar($contexto, CpSacdFila::idNom($fila)) !== false;
    }

    /**
     * Propaga `publicado_para`, que se escribe con un UPDATE directo sobre
     * `global.personas` y no pasa por el `Guardar()` de ningún repositorio.
     */
    public function sincronizarPublicadoPara(int $id_nom, ?string $json, ?CpSacdContexto $contexto = null): bool
    {
        $contexto ??= CpSacdContexto::desdeSesion();

        return $this->writer->actualizarPublicadoPara($contexto, $id_nom, $json);
    }
}
