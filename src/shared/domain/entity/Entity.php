<?php

namespace src\shared\domain\entity;

use src\shared\domain\events\EntidadModificada;
use src\shared\domain\traits\EmitsDomainEvents;
use src\shared\domain\traits\Hydratable;

/**
 * Clase base para Entidades del Dominio.
 *
 * Proporciona funcionalidad común para todas las entidades:
 * - Emisión de eventos de dominio (para auditoría y notificaciones)
 * - Conversión entre array y objeto (hidratación)
 *
 * EVENTOS DE DOMINIO:
 * Las entidades pueden emitir eventos cuando ocurren cambios importantes.
 * Estos eventos son procesados por listeners que registran los cambios en la tabla av_cambios
 * y pueden disparar notificaciones a usuarios interesados.
 *
 * USO DE EVENTOS:
 * - Usar marcarComoNueva() cuando se crea una nueva entidad
 * - Usar marcarComoModificada() cuando se actualiza una entidad existente
 * - Usar marcarComoEliminada() cuando se elimina una entidad
 *
 * Los eventos se despachan automáticamente desde el repositorio después de persistir en BD.
 *
 * @package orbix
 * @subpackage shared\domain\entity
 * @version 2.0
 * @created 2026-01-02
 */
abstract class Entity
{
    use EmitsDomainEvents;
    use Hydratable;

    /**
     * Obtiene el nombre de la entidad para los eventos.
     * Por defecto usa el nombre de la clase, pero puede ser sobrescrito.
     *
     * @return string Nombre de la entidad (ej: "Asistente", "Nota")
     */
    protected function getEntityName(): string
    {
        $path = explode('\\', static::class);
        return end($path);
    }

    /**
     * Intenta obtener el ID de actividad si la entidad lo tiene.
     * Esto permite que entidades relacionadas con actividades incluyan automáticamente
     * el id_activ en los eventos de dominio.
     *
     * @return int|null ID de la actividad o null si la entidad no tiene relación con actividades
     */
    protected function tryGetIdActiv(): ?int
    {
        // Intenta obtener id_activ si existe un getter
        if (method_exists($this, 'getId_activ')) {
            return $this->getId_activ();
        }

        /*
        // Intenta acceder a la propiedad directamente si es pública
        if (property_exists($this, 'id_activ')) {
            return $this->id_activ ?? null;
        }

        if (property_exists($this, 'iid_activ')) {
            return $this->iid_activ ?? null;
        }
        */

        return null;
    }

    /**
     * Marca la entidad como nueva (INSERT) y registra un evento de dominio.
     *
     * Usar este método en el repositorio después de crear una nueva entidad en BD.
     *
     * @param array $datosActuales Datos actuales para comparación (normalmente vacío en INSERT)
     * @return void
     */
    public function marcarComoNueva(array $datosActuales = []): void
    {
        $this->recordEvent(new EntidadModificada(
            objeto: $this->getEntityName(),
            tipoCambio: 'INSERT',
            idActiv: $this->tryGetIdActiv(),
            datosNuevos: $this->toArray(),
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca la entidad como modificada (UPDATE) y registra un evento de dominio.
     *
     * Usar este método en el repositorio después de actualizar una entidad en BD.
     *
     * @param array $datosActuales Datos anteriores antes de la modificación
     * @return void
     */
    public function marcarComoModificada(array $datosActuales): void
    {
        $this->recordEvent(new EntidadModificada(
            objeto: $this->getEntityName(),
            tipoCambio: 'UPDATE',
            idActiv: $this->tryGetIdActiv(),
            datosNuevos: $this->toArray(),
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca la entidad como eliminada (DELETE) y registra un evento de dominio.
     *
     * Usar este método en el repositorio antes de eliminar una entidad de BD.
     *
     * @param array $datosActuales Datos de la entidad antes de ser eliminada
     * @return void
     */
    public function marcarComoEliminada(array $datosActuales): void
    {
        $this->recordEvent(new EntidadModificada(
            objeto: $this->getEntityName(),
            tipoCambio: 'DELETE',
            idActiv: $this->tryGetIdActiv(),
            datosNuevos: [],
            datosActuales: $datosActuales
        ));
    }
}
