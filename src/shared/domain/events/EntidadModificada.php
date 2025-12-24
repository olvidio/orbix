<?php

namespace src\shared\domain\events;

/**
 * Evento de dominio que se emite cuando una entidad es modificada
 * Este evento genérico puede ser usado por cualquier entidad del sistema
 */
final readonly class EntidadModificada
{
    /**
     * @param string $objeto Nombre del objeto/entidad modificada (ej: "Asistente", "ActividadCargoSacd")
     * @param string $tipoCambio Tipo de cambio: 'INSERT', 'UPDATE', 'DELETE', 'FASE'
     * @param int $idActiv ID de la actividad relacionada
     * @param array $datosNuevos Datos nuevos/modificados
     * @param array $datosActuales Datos anteriores (para comparación en UPDATE)
     */
    public function __construct(
        public string $objeto,
        public string $tipoCambio,
        public int $idActiv,
        public array $datosNuevos,
        public array $datosActuales = []
    ) {}
}
