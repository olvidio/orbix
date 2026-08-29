<?php

declare(strict_types=1);

namespace src\personas\application;

/**
 * Resultado de reconciliar `cp_sacd` en un esquema.
 *
 * @see ResincronizarCpSacd
 */
final class CpSacdInformeEsquema
{
    /** @param list<string> $detalle columnas que difieren, por persona */
    private function __construct(
        public readonly string $esquema,
        public readonly int $origen = 0,
        public readonly int $destino = 0,
        public readonly int $altas = 0,
        public readonly int $cambios = 0,
        public readonly int $bajas = 0,
        public readonly array $detalle = [],
        public readonly string $omitido = '',
        public readonly string $error = '',
    ) {
    }

    /** @param list<string> $detalle */
    public static function reconciliado(
        string $esquema,
        int $origen,
        int $destino,
        int $altas,
        int $cambios,
        int $bajas,
        array $detalle = [],
    ): self {
        return new self($esquema, $origen, $destino, $altas, $cambios, $bajas, $detalle);
    }

    /** El esquema no aplica (no existe en una de las dos bases, o no se deduce la dl). */
    public static function omitido(string $esquema, string $motivo): self
    {
        return new self($esquema, omitido: $motivo);
    }

    public static function conError(string $esquema, string $mensaje): self
    {
        return new self($esquema, error: $mensaje);
    }

    public function esOmitido(): bool
    {
        return $this->omitido !== '';
    }

    public function tieneError(): bool
    {
        return $this->error !== '';
    }

    /**
     * @return array{
     *     esquema: string, origen: int, destino: int, altas: int, cambios: int,
     *     bajas: int, detalle: list<string>, omitido: string, error: string
     * }
     */
    public function toArray(): array
    {
        return [
            'esquema' => $this->esquema,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'altas' => $this->altas,
            'cambios' => $this->cambios,
            'bajas' => $this->bajas,
            'detalle' => $this->detalle,
            'omitido' => $this->omitido,
            'error' => $this->error,
        ];
    }
}
